/**
 * MS ERP • Public Website Interactive JavaScript
 * Self-contained JS supporting 10 UI Design Paradigms & Live Calculators
 */

document.addEventListener('DOMContentLoaded', () => {
    initParadigmSwitcher();
    initSpatialTilt();
    initRoiCalculator();
});

/**
 * 10 UI Design Paradigms Live Interactive Switcher
 */
function initParadigmSwitcher() {
    const tabs = document.querySelectorAll('.paradigm-tab-btn');
    const previewBox = document.getElementById('paradigm-preview-box');
    const titleEl = document.getElementById('paradigm-demo-title');
    const descEl = document.getElementById('paradigm-demo-desc');

    const descriptions = {
        skeuomorphism: {
            title: "1️⃣ Skeuomorphism Engine",
            desc: "Tactile metallic surfaces, embossed borders, realistic bevels, and physical volume depth mimicking real-world hardware controls."
        },
        neomorphism: {
            title: "2️⃣ Neomorphism Studio",
            desc: "Soft extruded plastic highlights and dual inset/outset shadows creating smooth sculpted surfaces that feel molded directly from the background."
        },
        glassmorphism: {
            title: "3️⃣ Glassmorphism Interface",
            desc: "Frosted translucent glass panels, backdrop blur filters, and light-refracting specular borders for futuristic multi-layered spatial depth."
        },
        claymorphism: {
            title: "4️⃣ Claymorphism Component",
            desc: "3D inflated clay geometry, soft inner lighting highlights, high border radii, and pill-smooth tactile volume."
        },
        minimalism: {
            title: "5️⃣ Minimalist Architecture",
            desc: "Ultra-clean typographical hierarchy, crisp monochrome contrast, generous whitespace, and zero visual noise for focused speed."
        },
        maximalism: {
            title: "6️⃣ Maximalist Studio",
            desc: "Vibrant high-energy color meshes, expressive glowing gradients, bold contrasts, and rich celebratory visual motion."
        },
        brutalism: {
            title: "7️⃣ Neo-Brutalist Layout",
            desc: "Stark black borders, raw high-contrast color blocks, heavy drop shadows, monospace typography, and unpolished structural energy."
        },
        liquidglass: {
            title: "8️⃣ Liquid Glass Canvas",
            desc: "Fluid animated refraction blobs, organic metallic glass surfaces, and dynamic ambient illumination."
        },
        bentogrid: {
            title: "9️⃣ Bento Grid Matrix",
            desc: "Asymmetric modular tile arrangement grouping enterprise features into structured, digestible dashboard panels."
        },
        spatialui: {
            title: "🔟 Spatial 3D UI",
            desc: "Perspective mouse-tracking z-axis tilt, floating ambient layers, and spatial lighting for immersive web experience."
        }
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const style = tab.getAttribute('data-style');

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            if (previewBox) {
                // Reset classes
                previewBox.className = 'paradigm-preview-box style-' + style;
            }

            if (titleEl && descriptions[style]) {
                titleEl.textContent = descriptions[style].title;
                descEl.textContent = descriptions[style].desc;
            }
        });
    });
}

/**
 * Spatial UI 3D Perspective Tilt Effect
 */
function initSpatialTilt() {
    const spatialCards = document.querySelectorAll('.spatial-card, .bento-card');

    spatialCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -10;
            const rotateY = ((x - centerX) / centerX) * 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        });
    });
}

/**
 * Interactive ROI & Time Savings Calculator
 */
function initRoiCalculator() {
    const userSlider = document.getElementById('roi-user-slider');
    const userCountEl = document.getElementById('roi-user-count');
    const hoursSavedEl = document.getElementById('roi-hours-saved');
    const costSavingsEl = document.getElementById('roi-cost-saved');

    if (!userSlider) return;

    function updateRoi() {
        const users = parseInt(userSlider.value) || 10;
        const hoursSavedPerWeek = users * 4.5;
        const monthlySavingsDollars = Math.round(hoursSavedPerWeek * 4 * 28); // $28/hr avg rate

        if (userCountEl) userCountEl.textContent = users + ' Active Team Members';
        if (hoursSavedEl) hoursSavedEl.textContent = Math.round(hoursSavedPerWeek * 4) + ' hrs/month';
        if (costSavingsEl) costSavingsEl.textContent = '$' + monthlySavingsDollars.toLocaleString() + ' / month';
    }

    userSlider.addEventListener('input', updateRoi);
    updateRoi();
}
