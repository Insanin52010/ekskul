document.addEventListener("DOMContentLoaded", () => {
    const cursorRing = document.createElement("div");
    cursorRing.style.cssText = `
        position: fixed !important;
        top: 0px;
        left: 0px;
        width: 44px;
        height: 44px;
        margin-left: -22px;
        margin-top: -22px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(167, 139, 250, 0.22) 0%, rgba(96, 165, 250, 0.14) 60%, rgba(250, 204, 21, 0.18) 100%);
        border: 2px solid rgba(167, 139, 250, 0.65);
        backdrop-filter: blur(2px);
        box-shadow: 0 0 18px rgba(167, 139, 250, 0.30);
        pointer-events: none;
        z-index: 999999;
        transition: width 0.2s ease, height 0.2s ease, margin 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        opacity: 0;
    `;
    document.body.appendChild(cursorRing);

    const cursorDot = document.createElement("div");
    cursorDot.style.cssText = `
        position: fixed !important;
        top: 0px;
        left: 0px;
        width: 8px;
        height: 8px;
        margin-left: -4px;
        margin-top: -4px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #7c3aed, #eab308);
        pointer-events: none;
        z-index: 1000000;
        opacity: 0;
    `;
    document.body.appendChild(cursorDot);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let ringX = mouseX;
    let ringY = mouseY;
    let lastBubbleTime = 0;

    const pastelColors = [
        "rgba(96, 165, 250, 0.65)",
        "rgba(250, 204, 21, 0.70)",
        "rgba(167, 139, 250, 0.65)"
    ];

    function createBubble(x, y, isClick = false) {
        const bubble = document.createElement("div");
        const size = isClick ? Math.random() * 24 + 14 : Math.random() * 14 + 8;
        const color = pastelColors[Math.floor(Math.random() * pastelColors.length)];

        bubble.style.cssText = `
            position: fixed !important;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
            margin-left: ${-size / 2}px;
            margin-top: ${-size / 2}px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.95), ${color});
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 10px ${color};
            pointer-events: none;
            z-index: 999998;
        `;
        document.body.appendChild(bubble);

        const driftX = (Math.random() - 0.5) * (isClick ? 140 : 50);
        const driftY = isClick ? (Math.random() - 0.5) * 140 : -(Math.random() * 65 + 30);

        const anim = bubble.animate([
            { transform: "translate(0px, 0px) scale(0.4)", opacity: 0.95 },
            { transform: `translate(${driftX * 0.6}px, ${driftY * 0.6}px) scale(1.2)`, opacity: 0.8, offset: 0.55 },
            { transform: `translate(${driftX}px, ${driftY}px) scale(0.1)`, opacity: 0 }
        ], {
            duration: isClick ? 750 : 950,
            easing: "cubic-bezier(0.16, 1, 0.3, 1)",
            fill: "forwards"
        });

        anim.onfinish = () => bubble.remove();
    }

    document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        cursorRing.style.opacity = "1";
        cursorDot.style.opacity = "1";
        cursorDot.style.transform = `translate3d(${mouseX}px, ${mouseY}px, 0)`;

        const now = Date.now();
        if (now - lastBubbleTime > 55) {
            createBubble(mouseX, mouseY, false);
            lastBubbleTime = now;
        }
    });

    document.addEventListener("mouseleave", () => {
        cursorRing.style.opacity = "0";
        cursorDot.style.opacity = "0";
    });

    document.addEventListener("mouseover", (e) => {
        if (e.target.closest("a, button, input, select, textarea, .ekskul-item, .stat-bento, label")) {
            cursorRing.style.width = "64px";
            cursorRing.style.height = "64px";
            cursorRing.style.marginLeft = "-32px";
            cursorRing.style.marginTop = "-32px";
            cursorRing.style.borderColor = "rgba(250, 204, 21, 0.9)";
            cursorRing.style.background = "radial-gradient(circle, rgba(254, 240, 138, 0.35) 0%, rgba(167, 139, 250, 0.22) 100%)";
        }
    });

    document.addEventListener("mouseout", (e) => {
        if (e.target.closest("a, button, input, select, textarea, .ekskul-item, .stat-bento, label")) {
            cursorRing.style.width = "44px";
            cursorRing.style.height = "44px";
            cursorRing.style.marginLeft = "-22px";
            cursorRing.style.marginTop = "-22px";
            cursorRing.style.borderColor = "rgba(167, 139, 250, 0.65)";
            cursorRing.style.background = "radial-gradient(circle, rgba(167, 139, 250, 0.22) 0%, rgba(96, 165, 250, 0.14) 60%, rgba(250, 204, 21, 0.18) 100%)";
        }
    });

    document.addEventListener("mousedown", (e) => {
        cursorRing.style.width = "30px";
        cursorRing.style.height = "30px";
        cursorRing.style.marginLeft = "-15px";
        cursorRing.style.marginTop = "-15px";

        for (let i = 0; i < 10; i++) {
            createBubble(e.clientX, e.clientY, true);
        }
    });

    document.addEventListener("mouseup", () => {
        cursorRing.style.width = "44px";
        cursorRing.style.height = "44px";
        cursorRing.style.marginLeft = "-22px";
        cursorRing.style.marginTop = "-22px";
    });

    function animateCursor() {
        ringX += (mouseX - ringX) * 0.16;
        ringY += (mouseY - ringY) * 0.16;
        cursorRing.style.transform = `translate3d(${ringX}px, ${ringY}px, 0)`;
        requestAnimationFrame(animateCursor);
    }
    animateCursor();
});