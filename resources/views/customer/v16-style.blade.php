@once
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
<style>
:root {
    --ink: #17130d;
    --muted: #17130d;
    --paper: #fffaf0;
    --panel: rgba(255,250,240, .86);
    --panel-strong: rgba(255,255,255, .72);
    --line: rgba(23,19,13, .13);
    --night: #17130d;
    --forest: #17130d;
    --gold: #c98a2e;
    --clay: #17130d;
    --sky: #17130d;
    --good: #17130d;
    --danger: #17130d;
    --shadow: 0 30px 90px rgba(23,19,13, .16);
    --radius: 30px;
}
* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    margin: 0;
    min-height: 100vh;
    color: var(--ink);
    font-family: "IBM Plex Sans Arabic", "Almarai", system-ui, sans-serif;
    background:
        radial-gradient(circle at 12% 10%, rgba(201,138,46, .32), transparent 28rem),
        radial-gradient(circle at 88% 4%, rgba(23,19,13, .18), transparent 30rem),
        linear-gradient(135deg, #fffaf0, #fffaf0 55%, #c98a2e);
}
body::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    opacity: .16;
    background-image:
        linear-gradient(rgba(23,19,13, .07) 1px, transparent 1px),
        linear-gradient(90deg, rgba(23,19,13, .06) 1px, transparent 1px);
    background-size: 42px 42px;
    mask-image: linear-gradient(to bottom, #000, transparent 84%);
}
a { color: inherit; text-decoration: none; }
button, input, select, textarea { font: inherit; }
.shell { width: min(1120px, calc(100% - 32px)); margin: auto; padding: 18px 0 56px; }
.top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: 26px;
    background: rgba(255,250,240, .76);
    backdrop-filter: blur(16px);
}
.brand { display: flex; align-items: center; gap: 10px; }
.mark {
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    border-radius: 15px;
    color: var(--paper);
    background: conic-gradient(from 210deg, #17130d, #17130d, #c98a2e, #17130d, #17130d);
    font-weight: 900;
}
.brand strong { display: block; }
.brand small { display: block; color: var(--muted); }
.nav { display: flex; flex-wrap: wrap; gap: 8px; }
.nav a, .button, button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0 16px;
    border: 1px solid rgba(23,19,13, .13);
    border-radius: 999px;
    background: rgba(255,255,255, .54);
    font-weight: 800;
    cursor: pointer;
}
.primary, button.primary { background: var(--night); color: var(--paper); }
.hero, .card {
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--panel);
    box-shadow: var(--shadow);
}
.hero {
    display: grid;
    grid-template-columns: 1.05fr .95fr;
    gap: 16px;
    padding: clamp(20px, 3vw, 34px);
    overflow: hidden;
}
.hero h1, .page-title {
    margin: 12px 0 0;
    font-size: clamp(26px, 3.8vw, 44px);
    line-height: 1.12;
    letter-spacing: -.9px;
}
.lead { max-width: 680px; color: var(--muted); font-size: clamp(14px, 1.15vw, 17px); line-height: 1.75; }
.kicker, .tag {
    display: inline-flex;
    width: fit-content;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(201,138,46, .11);
    color: #17130d;
    font-weight: 900;
    font-size: 12px;
}
.grid { display: grid; gap: 12px; }
.grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.grid.four { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.card { padding: 20px; }
.card h2, .card h3 { margin: 0 0 10px; }
.card p { margin: 0; color: var(--muted); line-height: 1.75; }
.section { margin-top: 20px; }
.field { display: grid; gap: 7px; margin-bottom: 13px; }
.field label { font-weight: 900; }
.field input, .field select, .field textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    background: rgba(255,255,255, .62);
    padding: 13px 14px;
    color: var(--ink);
}
.field textarea { min-height: 110px; resize: vertical; }
.error { margin: 0 0 12px; color: var(--danger); font-weight: 800; }
.notice { margin: 0 0 12px; padding: 12px 14px; border-radius: 16px; background: rgba(23,19,13, .12); color: var(--good); font-weight: 800; }
.theme { position: relative; overflow: hidden; }
.theme::after {
    content: attr(data-score);
    position: absolute;
    inset-inline-end: 18px;
    bottom: -8px;
    color: rgba(23,19,13, .06);
    font-size: 54px;
    font-weight: 900;
}
.metric { padding: 14px; border-radius: 18px; background: rgba(255,255,255, .48); border: 1px solid rgba(23,19,13, .08); }
.metric strong { display: block; font-size: 22px; }
.list { display: grid; gap: 10px; }
.list a, .list div, .line-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255,255,255, .46);
    border: 1px solid rgba(23,19,13, .08);
    font-weight: 800;
}
.dark { background: linear-gradient(145deg, #17130d, #17130d); color: var(--paper); }
.dark p, .dark small { color: rgba(255,250,240, .75); }
.checks { display: grid; gap: 9px; }
.check { display: flex; gap: 9px; align-items: flex-start; padding: 10px 0; }
.check input { margin-top: 6px; }
.footer-note { margin-top: 18px; color: var(--muted); line-height: 1.8; }
.choice { display: block; cursor: pointer; }
.choice input { margin-inline-end: 8px; }
.choice .card { height: 100%; transition: transform .18s ease, border-color .18s ease; }
.choice input:checked + .card { border-color: rgba(23,19,13, .72); transform: translateY(-2px); }
.split { display: grid; grid-template-columns: .9fr 1.1fr; gap: 18px; align-items: start; }
.muted { color: var(--muted); }
.small { font-size: 13px; }
@media (max-width: 940px) {
    .hero, .split, .grid.two, .grid.three, .grid.four { grid-template-columns: 1fr; }
    .top { align-items: flex-start; flex-direction: column; }
    .hero h1, .page-title { letter-spacing: -.6px; }
}
</style>
@endonce
