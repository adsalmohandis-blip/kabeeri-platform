@once
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=almarai:400,700,800|ibm-plex-sans-arabic:400,500,600,700" rel="stylesheet" />
@include('components.theme-foundation')
<style>
:root {
    --ink: #000000;
    --muted: rgba(0,0,0,.62);
    --paper: #f0f0f0;
    --sand: #f0f0f0;
    --panel: rgba(250,250,250,.42);
    --line: rgba(0,0,0,.12);
    --night: #000000;
    --radius: 24px;
}
* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    margin: 0;
    min-height: 100vh;
    color: var(--ink);
    font-family: "IBM Plex Sans Arabic", "Almarai", system-ui, sans-serif;
    background: linear-gradient(180deg, var(--paper) 0%, var(--sand) 100%);
}
a { color: inherit; text-decoration: none; }
button, input, select, textarea { font: inherit; }
.shell { width: min(1120px, calc(100% - 32px)); margin: auto; padding: 16px 0 48px; }
.top {
    position: sticky;
    top: 12px;
    z-index: var(--kbr-nav-layer, 1000);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    padding: 10px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: rgba(240,240,240,.9);
    backdrop-filter: blur(16px);
}
.brand { display: flex; align-items: center; gap: 10px; font-weight: 900; }
.mark {
    display: grid;
    place-items: center;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    color: var(--paper);
    background: #000000;
    font-weight: 900;
}
.brand small { display: block; color: var(--muted); font-size: 12px; }
.nav { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.nav a, .button, button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 0 13px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: rgba(250,250,250,.55);
    color: #000000;
    font-size: 13px;
    font-weight: 900;
    cursor: pointer;
}
.primary, button.primary { background: #000000; color: #f0f0f0; border-color: #000000; }
.start-grid {
    display: grid;
    grid-template-columns: minmax(0,1fr) minmax(330px,.72fr);
    gap: 22px;
    align-items: start;
    padding: clamp(24px, 5vw, 58px) 0 24px;
}
.kicker, .tag {
    display: inline-flex;
    width: fit-content;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    background: rgba(0,0,0,.08);
    padding: 8px 12px;
    color: #000000;
    font-size: 12px;
    font-weight: 900;
}
h1, .page-title {
    margin: 14px 0 0;
    max-width: 760px;
    font-size: clamp(34px, 5.3vw, 64px);
    line-height: .98;
    letter-spacing: -.055em;
}
.lead { max-width: 650px; color: var(--muted); font-size: clamp(14px,1.25vw,17px); line-height: 1.72; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 22px; }
.form-card, .quiet-card {
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: rgba(250,250,250,.42);
    padding: 18px;
}
.form-card.dark { background: #000000; color: #f0f0f0; }
.form-card.dark p, .form-card.dark small { color: rgba(240,240,240,.72); }
.form-card.dark input, .form-card.dark select { background: #f0f0f0; color: #000000; }
.form-card h2, .quiet-card h2 { margin: 0 0 8px; font-size: 20px; }
.field { display: grid; gap: 6px; margin-bottom: 11px; }
.field label { font-size: 12px; font-weight: 900; }
.field input, .field select, .field textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 15px;
    background: rgba(250,250,250,.78);
    padding: 10px 12px;
    color: #000000;
}
.error { margin: 0 0 12px; color: #f0f0f0; font-weight: 900; }
.grid { display: grid; gap: 10px; }
.grid.two { grid-template-columns: repeat(2,minmax(0,1fr)); }
.section { padding: 24px 0; border-top: 1px solid var(--line); }
.section-title { display: grid; grid-template-columns: minmax(0,.9fr) minmax(0,1.1fr); gap: 18px; align-items: end; margin-bottom: 10px; }
.section-title p { margin: 0; }
.line-list { display: grid; border-top: 1px solid var(--line); }
.line-item {
    display: grid;
    grid-template-columns: minmax(0,1fr) auto;
    gap: 12px;
    align-items: center;
    padding: 13px 0;
    border-bottom: 1px solid rgba(0,0,0,.1);
    font-weight: 900;
}
.line-item span { display: grid; gap: 4px; min-width: 0; }
.line-item small { color: var(--muted); font-size: 12px; font-weight: 800; line-height: 1.55; }
.line-item b { color: #000000; font-size: 12px; }
.step-list { display: grid; gap: 0; margin-top: 22px; max-width: 690px; border-top: 1px solid var(--line); }
.step-line { display: grid; grid-template-columns: 2rem minmax(0,1fr); gap: 12px; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,.1); }
.step-line strong { font-size: 13px; }
.number { display: grid; width: 28px; height: 28px; place-items: center; border-radius: 999px; background: #000000; color: #f0f0f0; font-size: 11px; font-weight: 900; }
@media (max-width: 940px) {
    .top { align-items: stretch; border-radius: 24px; flex-direction: column; }
    .nav, .hero-actions { align-items: stretch; flex-direction: column; }
    .nav a, .button, button { width: 100%; }
    .start-grid, .section-title, .grid.two, .line-item { grid-template-columns: 1fr; }
    h1, .page-title { letter-spacing: -.04em; }
}
</style>
@endonce
