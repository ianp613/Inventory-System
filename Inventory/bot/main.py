import logging
import json
import os
import time

# Disable INFO logs from httpx and telegram
logging.getLogger("httpx").setLevel(logging.CRITICAL)
logging.getLogger("telegram.ext").setLevel(logging.CRITICAL)
logging.getLogger("telegram").setLevel(logging.CRITICAL)

from telegram import Update
from telegram.ext import ApplicationBuilder, MessageHandler, ContextTypes, filters
import logging
import sole  # Your custom business logic module

# Bot config
BOT_TOKEN = "7585903473:AAGAuKu6c4kOnNvDB0URwdBt_9gK66uD4HU"
GROUP_CHAT_ID = -1002850193001
MAX_LENGTH = 3000  # Telegram's message size limit

# 🧩 Log setup
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO
)

# 📦 Message splitter for long replies
def split_message(text):
    return [text[i:i+MAX_LENGTH] for i in range(0, len(text), MAX_LENGTH)]

# 🎯 Main message reply handler
async def reply(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if update.effective_chat.id == GROUP_CHAT_ID:
        try:
            user_message = update.message.text
            bot_reply = sole.index(user_message)

            try:
                # Try to parse as JSON
                parsed = json.loads(bot_reply)

                if isinstance(parsed, list):
                    # It's an array from PHP
                    print("It's an array from PHP")
                    if parsed[0] == "string":
                        for part in split_message(parsed[1]):
                            part = part.replace("[", "   ")
                            part = part.replace("---", "🍂")
                            part = part.replace("\\", "")
                            part = part.replace("br|", "\n")
                            part = part.strip('"')
                            await update.message.reply_text(part, parse_mode="HTML")
                            # await update.message.reply_text(part)
                    
                    if parsed[0] == "file":
                        parsed[1] = parsed[1].replace("[", "   ")
                        parsed[1] = parsed[1].replace("---", "🍂")
                        parsed[1] = parsed[1].replace("\\", "")
                        parsed[1] = parsed[1].replace("br|", "\n")
                        parsed[1] = parsed[1].strip('"')

                        # if os.path.exists(parsed[2]):
                        #     with open(parsed[2], "rb") as photo:
                        #         await update.message.reply_photo(photo=photo, caption=parsed[1], parse_mode="HTML")
                        # else:
                        #     await update.message.reply_text(parsed[1]+"⚠️ Map layout is not yet generated, you can log into the system and manualy add camera to generate map layout.", parse_mode="HTML")

                        # Wait until the file exists and is ready (up to 10 seconds)
                        timeout = 10  # seconds
                        elapsed = 0
                        while not os.path.exists(parsed[2]):
                            time.sleep(1)
                            elapsed += 1
                            if elapsed >= timeout:
                                break

                        # Proceed only if the file is now available
                        if os.path.exists(parsed[2]):
                            with open(parsed[2], "rb") as photo:
                                await update.message.reply_photo(photo=photo, caption=parsed[1], parse_mode="HTML")
                        else:
                            await update.message.reply_text(
                                parsed[1] + "⚠️ The map layout is currently unavailable. Please log into the system and manually add a camera to initiate its generation.",
                                parse_mode="HTML"
                            )
                else:
                    # It's a JSON string or object, not an array
                    parsed = parsed.replace("[", "   ")
                    parsed = parsed.replace("---", "🍂")
                    parsed = parsed.replace("\\", "")
                    parsed = parsed.replace("br|", "\n")
                    parsed = parsed.strip('"')
                    print("It's a JSON string or object, not an array")
                    await update.message.reply_text(str(parsed), parse_mode="HTML")

            except json.JSONDecodeError:
                # Not JSON — treat as plain string
                await update.message.reply_text(bot_reply, parse_mode="HTML")

            # for part in split_message(bot_reply):
            #     part = part.replace("[", "   ")
            #     part = part.replace("---", "🍂")
            #     part = part.replace("\\", "")
            #     part = part.replace("br|", "\n")
            #     part = part.strip('"')
            #     await update.message.reply_text(part, parse_mode="HTML")
                # await update.message.reply_text(part)

        except Exception as e:
            logging.error("Error in reply(): %s", str(e), exc_info=True)
            try:
                await update.message.reply_text("⚠️ Something went wrong while processing your request. [python]")
            except:
                pass  # Prevent bot from crashing if reply fails

# ❗ Global error handler
async def error_handler(update: object, context: ContextTypes.DEFAULT_TYPE):
    logging.error("Exception while handling update:", exc_info=context.error)

    # Attempt to notify the user if possible
    if isinstance(update, Update) and update.message:
        try:
            await update.message.reply_text("⚠️ Bot encountered an error. Please try again later. [python]")
        except:
            pass

# 🛎️ Entry point
if __name__ == "__main__":
    app = ApplicationBuilder().token(BOT_TOKEN).build()

    # Add message handler (ignores commands)
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, reply))

    # Register error handler
    app.add_error_handler(error_handler)

    print("Wifi Team Inventory Bot is running ...")
    print("Do not close this window ...")
    app.run_polling()