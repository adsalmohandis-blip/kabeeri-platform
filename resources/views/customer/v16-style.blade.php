@once
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
<style>
:root {
    --ink: #111111;
    --muted: rgba(17,17,17,.64);
    --paper: #f1eadc;
    --panel: rgba(255,255,255, .44);
    --panel-strong: rgba(255,255,255, .74);
    --line: rgba(17,17,17, .13);
    --night: #111111;
    --forest: #111111;
    --gold: #111111;
    --clay: #111111;
    --sky: #111111;
    --good: #111111;
    --danger: #111111;
    --shadow: 0 18px 58px rgba(17,17,17, .07);
    --radius: 24px;
}
* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    margin: 0;
    min-height: 100vh;
    color: var(--ink);
    font-family: "IBM Plex Sans Arabic", "Almarai", system-ui, sans-serif;
    background: linear-gradient(180deg, #f1eadc 0%, #ebe1d0 100%);
}
a { color: inherit; text-decoration: none; }
button, input, select, textarea { font: inherit; }
.shell { width: min(1120px, calc(100% - 32px)); margin: auto; padding: 16px 0 48px; }
.top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding: 11px;
    border: 1px solid var(--line);
    border-radius: 26px;
    background: rgba(241,234,220, .86);
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
    background: #111111;
    font-weight: 900;
}
.brand strong { display: block; }
.brand small { display: block; color: var(--muted); }
.nav { display: flex; flex-wrap: wrap; gap: 8px; }
.nav a, .button, button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 14px;
    border: 1px solid rgba(17,17,17, .13);
    border-radius: 999px;
    background: rgba(255,255,255, .58);
    font-weight: 800;
    cursor: pointer;
}
.primary, button.primary { background: var(--night); color: var(--paper); }
.hero, .card {
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--panel);
    box-shadow: none;
}
.hero {
    display: grid;
    grid-template-columns: 1.05fr .95fr;
    gap: 14px;
    padding: clamp(18px, 2.6vw, 28px);
    overflow: hidden;
}
.hero h1, .page-title {
    margin: 12px 0 0;
    font-size: clamp(22px, 3vw, 34px);
    line-height: 1.12;
    letter-spacing: -.9px;
}
.lead { max-width: 680px; color: var(--muted); font-size: clamp(13px, 1vw, 15px); line-height: 1.7; }
.kicker, .tag {
    display: inline-flex;
    width: fit-content;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(17,17,17, .08);
    color: #111111;
    font-weight: 900;
    font-size: 12px;
}
.grid { display: grid; gap: 10px; }
.grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.grid.four { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.card { padding: 16px; }
.card h2, .card h3 { margin: 0 0 10px; }
.card p { margin: 0; color: var(--muted); line-height: 1.7; font-size: 13px; }
.section { margin-top: 16px; }
.section-title { display: flex; align-items: end; justify-content: space-between; gap: 14px; margin-bottom: 12px; }
.field { display: grid; gap: 6px; margin-bottom: 12px; }
.field label { font-weight: 900; }
.field input, .field select, .field textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    background: rgba(255,255,255, .76);
    padding: 11px 13px;
    color: var(--ink);
}
.field textarea { min-height: 110px; resize: vertical; }
.error { margin: 0 0 12px; color: var(--danger); font-weight: 800; }
.notice { margin: 0 0 12px; padding: 12px 14px; border-radius: 16px; background: rgba(17,17,17, .12); color: var(--good); font-weight: 800; }
.theme { position: relative; overflow: hidden; }
.theme::after {
    content: attr(data-score);
    position: absolute;
    inset-inline-end: 18px;
    bottom: -8px;
    color: rgba(17,17,17, .06);
    font-size: 44px;
    font-weight: 900;
}
.metric { padding: 10px 0; border-bottom: 1px solid rgba(17,17,17, .1); background: transparent; }
.metric strong { display: block; font-size: 18px; }
.metric:last-child { border-bottom: 0; }
.list { display: grid; gap: 0; }
.panel-list { border-top: 1px solid rgba(17,17,17,.12); }
.list a, .list div, .line-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-radius: 0;
    background: transparent;
    border: 0;
    border-bottom: 1px solid rgba(17,17,17, .1);
    font-weight: 800;
}
.line-item span { display: grid; gap: 4px; }
.line-item small { color: var(--muted); font-size: 12px; font-weight: 800; line-height: 1.55; }
.line-item b { color: #111; font-size: 12px; }
.list a:last-child, .list div:last-child, .line-item:last-child { border-bottom: 0; }
.dark { background: linear-gradient(145deg, #111111, #111111); color: var(--paper); }
.dark p, .dark small { color: rgba(241,234,220, .75); }
.checks { display: grid; gap: 9px; }
.check { display: flex; gap: 9px; align-items: flex-start; padding: 10px 0; }
.check input { margin-top: 6px; }
.footer-note { margin-top: 16px; color: var(--muted); line-height: 1.7; font-size: 13px; }
.choice { display: block; cursor: pointer; }
.choice input { margin-inline-end: 8px; }
.choice .card { height: 100%; transition: transform .18s ease, border-color .18s ease; }
.choice input:checked + .card { border-color: rgba(17,17,17, .72); transform: translateY(-2px); }
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
