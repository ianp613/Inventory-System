/**
 * visualizer.js
 * Usage:
 *   visualizer(container, "wave")
 *   visualizer(container, "bar")
 */
function visualizer(container, type) {

    const canvas = document.createElement("canvas");
    canvas.style.cssText = "display:block;width:100%;height:100%;";
    container.style.position = container.style.position || "relative";
    container.appendChild(canvas);
    const ctx = canvas.getContext("2d");
    let running = true;

    function resize() {
        canvas.width  = container.offsetWidth  || 400;
        canvas.height = container.offsetHeight || 160;
    }
    resize();
    if (typeof ResizeObserver !== "undefined") {
        new ResizeObserver(resize).observe(container);
    } else {
        window.addEventListener("resize", resize);
    }

    // ─────────────────────────────────────────────
    //  WAVE mode
    // ─────────────────────────────────────────────
    if (type === "wave") {
        const waves = [
        { color: "#ff4d4d", phase: 0.00, freq: 1.0, amp: 0.82 },
        { color: "#ff9900", phase: 0.30, freq: 1.1, amp: 0.70 },
        { color: "#ffe600", phase: 0.60, freq: 1.2, amp: 0.58 },
        { color: "#00e676", phase: 0.90, freq: 1.3, amp: 0.46 },
        { color: "#00b0ff", phase: 1.20, freq: 1.4, amp: 0.58 },
        { color: "#651fff", phase: 1.50, freq: 1.5, amp: 0.70 },
        { color: "#e040fb", phase: 1.80, freq: 1.6, amp: 0.82 },
        ];
        let t = 0;
        function drawWave(wave) {
        const W = canvas.width, H = canvas.height, cx = W/2, cy = H/2;
        const maxAmp = (H/2)*0.88;
        ctx.beginPath(); ctx.strokeStyle = wave.color; ctx.lineWidth = 2.2;
        ctx.shadowBlur = 12; ctx.shadowColor = wave.color;
        for (let x = 0; x <= W; x++) {
            const nx = (x-cx)/cx;
            const env = Math.exp(-3.5*nx*nx);
            const y = cy + Math.sin(nx*Math.PI*5*wave.freq + t*wave.freq + wave.phase)*maxAmp*wave.amp*env;
            x===0 ? ctx.moveTo(x,y) : ctx.lineTo(x,y);
        }
        ctx.stroke(); ctx.shadowBlur = 0;
        }
        function loop() {
        ctx.clearRect(0,0,canvas.width,canvas.height);
        waves.forEach(w=>drawWave(w));
        t+=0.04;
        requestAnimationFrame(loop);
        }
        loop();
        return;
    }

    // ─────────────────────────────
    // BAR
    // ─────────────────────────────
    if (type === "bar") {

        const CELL = 4, GAP = 1, STEP = 5;
        let bars = [], t = 0;

        function loop() {

            const W = canvas.width;
            const H = canvas.height;

            ctx.clearRect(0, 0, W, H);

            const count = Math.floor(W / STEP);

            if (bars.length !== count) {
                bars = Array.from({ length: count }, () => ({
                    current: Math.random() * 0.3,
                    vel: 0,
                    phase: Math.random() * Math.PI * 2,
                    speed: 0.012 + Math.random() * 0.018,
                }));
            }

            const maxBarH = H * 0.93;
            const offsetX = (W - count * STEP) / 2;
            const hueOffset = (t * 0.6) % 360;

            bars.forEach((bar, i) => {

                bar.current += (Math.random() - 0.5) * 0.1;
                bar.current = Math.max(0.05, Math.min(1, bar.current));

                const barH = bar.current * maxBarH;
                const x = offsetX + i * STEP;
                const cells = Math.floor(barH / STEP);

                const hue = (hueOffset + i * 2) % 360;

                for (let c = 0; c < cells; c++) {

                    ctx.fillStyle = `hsl(${hue},100%,60%)`;

                    ctx.fillRect(
                        x,
                        H - (c + 1) * STEP,
                        CELL,
                        CELL
                    );
                }
            });

            t++;

            if (running) requestAnimationFrame(loop);
        }

        loop();
    }

    // ─────────────────────────────────────────────
    //  HEARTBEAT mode
    // ─────────────────────────────────────────────
    if (type === "heartbeat") {

        let t = 0;

        function drawHeartbeat() {
            const W = canvas.width;
            const H = canvas.height;
            const cy = H / 2;

            ctx.clearRect(0, 0, W, H);

            // Rainbow gradient matching reference image
            const gradient = ctx.createLinearGradient(0, 0, W, 0);
            gradient.addColorStop(0.00, "#4FC3F7");
            gradient.addColorStop(0.15, "#42A5F5");
            gradient.addColorStop(0.30, "#5E35B1");
            gradient.addColorStop(0.45, "#7C4DFF");
            gradient.addColorStop(0.60, "#FF5ECF");
            gradient.addColorStop(0.75, "#FF9800");
            gradient.addColorStop(0.90, "#FFC107");
            gradient.addColorStop(1.00, "#FFF176");

            ctx.beginPath();

            for (let x = 0; x <= W; x++) {

                const nx = x / W;

                // Shape similar to reference image
                const waveA =
                    Math.sin(nx * Math.PI * 24 + t * 0.08);

                const waveB =
                    Math.sin(nx * Math.PI * 10 - t * 0.04) * 0.8;

                const waveC =
                    Math.sin(nx * Math.PI * 44 + t * 0.02) * 0.25;

                // Pulsing amplitude envelope
                const envelope =
                    0.55 +
                    0.45 * (
                        0.5 +
                        0.5 * Math.sin(nx * Math.PI * 3 + t * 0.01)
                    );

                const y =
                    cy +
                    (waveA + waveB + waveC) *
                    (H * 0.12) *
                    envelope;

                if (x === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }

            ctx.lineWidth = 3;
            ctx.strokeStyle = gradient;
            ctx.lineCap = "round";
            ctx.lineJoin = "round";

            // Soft glow
            ctx.shadowBlur = 16;
            ctx.shadowColor = "#c77dff";

            ctx.stroke();

            ctx.shadowBlur = 0;

            t += 1;
            requestAnimationFrame(drawHeartbeat);
        }

        drawHeartbeat();
        return;
    }
}