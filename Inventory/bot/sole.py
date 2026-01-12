import requests
import json
import os

def load_config():
    try:
        # Adjust the path if needed
        config_path = os.path.join(os.path.dirname(__file__), 'config.json')
        with open(config_path, 'r') as f:
            config = json.load(f)
        return config.get('host'), config.get('port')
    except Exception as e:
        raise RuntimeError(f"Failed to load config.json: {e}")

def index(user_message: str) -> str:
    try:
        host, port = load_config()
        url = f"http://{host}:{port}/controllers/telegram_bot/main.php"
        payload = {'message': user_message}
        response = requests.post(url, data=payload)
        return response.text
    except Exception as e:
        return f"Error contacting Wifi Team Inventory Server: {e} [python]"