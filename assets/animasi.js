document.addEventListener("DOMContentLoaded", () => {
    // 1. Lingkaran Transparan Mengikuti Kursor (Smooth Follower)
    const cursorRing = document.createElement("div");
    cursorRing.className = "cursor-follower-ring";
    document.body.appendChild(cursorRing);

    const cursorDot = document.createElement("div");
    cursorDot.className = "cursor-follower-dot";
    document.body.appendChild(cursorDot);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let ringX = mouseX;
    let ringY = mouseY;
    let lastBubbleTime = 0;

    // Warna pastel biru, kuning, ungu untuk gelembung
    const pastelColors = [
        "rgba(96, 165, 250, 0.55)",  // Biru Pastel
        "rgba(250, 204, 21, 0.55)",  // Kuning Pastel
        "rgba(167, 139, 250, 0.55)"  // Ungu Pastel
    ];

    // Fungsi pembuat gelembung ("blubub")
    function createBubble(x, y, isClick = false) {
        const bubble = document.createElement("span");
        bubble.className = isClick ? "pastel-bubble click-pop" : "pastel-bubble trail-float";

        const size = isClick ? Math.random() * 22 + 14 : Math.random() * 14 + 8;
        const color = pastelColors[Math.floor(Math.random() * pastelColors.length)];

        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.left = `${x}px`;
        bubble.style.top = `${y}px`;
        bubble.style.background = `radial-gradient(circle at 30% 30%, rgba(255,255,255,0.9), ${color})`;
        bubble.style.boxShadow = `0 0 10px ${color}`;

        // Arah gerak acak untuk efek percikan gelembung
        const driftX = (Math.random() - 0.5) * (isClick ? 120 : 45);
        const driftY = isClick ? (Math.random() - 0.5) * 120 : -(Math.random() * 55 + 25);

        bubble.style.setProperty("--dx", `${driftX}px`);
        bubble.style.setProperty("--dy", `${driftY}px`);

        document.body.appendChild(bubble);

        setTimeout(() => {
            bubble.remove();
        }, isClick ? 750 : 950);
    }

    // Event saat kursor bergerak
    document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        cursorDot.style.transform = `translate(${mouseX}px, ${mouseY}px)`;

        // Munculkan gelembung kecil secara berkala saat kursor digerakkan
        const now = Date.now();
        if (now - lastBubbleTime > 65) {
            createBubble(mouseX, mouseY, false);
            lastBubbleTime = now;
        }
    });

    // Membesar sedikit saat mengarah ke tombol / link / kartu
    document.addEventListener("mouseover", (e) => {
        if (e.target.closest("a, button, input, select, textarea, .ekskul-item, .stat-bento, label")) {
            cursorRing.classList.add("cursor-hover");
        }
    });

    document.addEventListener("mouseout", (e) => {
        if (e.target.closest("a, button, input, select, textarea, .ekskul-item, .stat-bento, label")) {
            cursorRing.classList.remove("cursor-hover");
        }
    });

    // Event saat mengklik sesuatu -> Muncul banyak gelembung ("blubub" meletup)
    document.addEventListener("mousedown", (e) => {
        cursorRing.classList.add("cursor-click");
        for (let i = 0; i < 9; i++) {
            createBubble(e.clientX, e.clientY, true);
        }
    });

    document.addEventListener("mouseup", () => {
        cursorRing.classList.remove("cursor-click");
    });

    // Animasi halus lingkaran mengikuti kursor (lerp)
    function animateCursor() {
        ringX += (mouseX - ringX) * 0.16;
        ringY += (mouseY - ringY) * 0.16;
        cursorRing.style.transform = `translate(${ringX}px, ${ringY}px)`;
        requestAnimationFrame(animateCursor);
    }
    animateCursor();
});