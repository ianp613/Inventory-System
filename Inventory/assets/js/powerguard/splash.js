function splash(timer = 1, iconColor = "primary", bgColor = "#ffffff") {
  const colorMap = {
    primary: "#007bff",
    danger: "#dc3545",
    warning: "#ffc107",
    success: "#28a745",
    info: "#17a2b8",
    dark: "#343a40",
    light: "#ffffff"
  };

  // Remove any existing splash before creating a new one
  document.querySelectorAll("[data-splash]").forEach(el => el.remove());

  // Create splash container
  const splashDiv = document.createElement("div");
  splashDiv.setAttribute("data-splash", "true");
  splashDiv.style.position = "fixed";
  splashDiv.style.top = "0";
  splashDiv.style.left = "0";
  splashDiv.style.width = "100%";
  splashDiv.style.height = "100%";
  splashDiv.style.display = "flex";
  splashDiv.style.alignItems = "center";
  splashDiv.style.justifyContent = "center";
  splashDiv.style.zIndex = "9999";
  splashDiv.style.backgroundColor = bgColor;
  splashDiv.style.opacity = "1";
  splashDiv.style.transition = "opacity 1s ease";

  // Rotating icon
  const icon = document.createElement("div");
  icon.style.width = "60px";
  icon.style.height = "60px";
  icon.style.border = "6px solid transparent";
  icon.style.borderTopColor = colorMap[iconColor] || iconColor;
  icon.style.borderRadius = "50%";
  icon.style.animation = "splash-spin 1s linear infinite";

  splashDiv.appendChild(icon);
  document.body.appendChild(splashDiv);

  // Inject keyframes (only once)
  if (!document.getElementById("splash-keyframes")) {
    const style = document.createElement("style");
    style.id = "splash-keyframes";
    style.innerHTML = `
      @keyframes splash-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
  }

  // Force a reflow so the browser registers the starting opacity
  // before we change it — guarantees the transition actually plays
  void splashDiv.offsetHeight;

  // Fade out after timer
  setTimeout(() => {
    splashDiv.style.opacity = "0";
    splashDiv.addEventListener("transitionend", () => splashDiv.remove(), { once: true });
    splashDiv.remove()
  }, timer * 1000);
}