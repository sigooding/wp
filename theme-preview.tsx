"use client";

import { useState, type CSSProperties } from "react";
import { Check, Search, UserRound } from "lucide-react";

const colors = [
  { name: "Forest", color: "#245640", deep: "#183c30" },
  { name: "Ocean", color: "#326c7a", deep: "#1b4653" },
  { name: "Slate", color: "#4c6171", deep: "#273c49" },
  { name: "Terracotta", color: "#a25f45", deep: "#673e30" },
];

function Swatches({ active, onPick }: { active: (typeof colors)[number]; onPick: (c: (typeof colors)[number]) => void }) {
  return <div className="theme-swatches">{colors.map((c) => <button key={c.name} type="button" title={c.name} aria-label={`${c.name} colour`} aria-pressed={active.name === c.name} onClick={() => onPick(c)} style={{ background: c.color }}><span>{active.name === c.name && <Check size={14} />}</span></button>)}</div>;
}

function Toggle({ label, on, onChange }: { label: string; on: boolean; onChange: (v: boolean) => void }) {
  return <div className="theme-toggle-row"><span>{label}</span><button type="button" className={`theme-toggle ${on ? "on" : ""}`} onClick={() => onChange(!on)} aria-pressed={on} aria-label={`Toggle ${label}`}><span /></button></div>;
}

export default function ThemePreview() {
  const [active, setActive] = useState(colors[0]);
  const [search, setSearch] = useState(true);
  const [account, setAccount] = useState(true);
  const [topbar, setTopbar] = useState(true);
  const [sticky, setSticky] = useState(true);
  const [contact, setContact] = useState(true);
  const [catalogue, setCatalogue] = useState(true);
  const [size, setSize] = useState("14");
  const [radius, setRadius] = useState("9");

  return <div className="theme-preview-panel">
    <div className="theme-preview-controls">
      <div><p className="theme-mini-label">MAKE IT YOURS</p><h3>Preview the possibilities.</h3><p>These mirror real controls under Appearance → Customize → Trade Portal Options.</p></div>
      <div><span className="theme-control-label">ACCENT COLOUR</span><Swatches active={active} onPick={setActive} /></div>
      <div><span className="theme-control-label">BASE TEXT SIZE</span><div className="theme-chip-row">{["13", "14", "15", "16"].map((v) => <button key={v} type="button" className={`theme-chip ${size === v ? "on" : ""}`} onClick={() => setSize(v)} aria-pressed={size === v}>{v}px</button>)}</div></div>
      <div><span className="theme-control-label">CARD CORNERS</span><div className="theme-chip-row">{[["4", "Subtle"], ["9", "Soft"], ["16", "Rounded"]].map(([v, l]) => <button key={v} type="button" className={`theme-chip ${radius === v ? "on" : ""}`} onClick={() => setRadius(v)} aria-pressed={radius === v}>{l}</button>)}</div></div>
      <div className="theme-toggle-group">
        <Toggle label="Announcement bar" on={topbar} onChange={setTopbar} />
        <Toggle label="Sticky header" on={sticky} onChange={setSticky} />
        <Toggle label="Header search" on={search} onChange={setSearch} />
        <Toggle label="Account link" on={account} onChange={setAccount} />
        <Toggle label="Footer contact" on={contact} onChange={setContact} />
        <Toggle label="Catalogue-only mode" on={catalogue} onChange={setCatalogue} />
      </div>
      <small>Interactive preview only</small>
    </div>

    <div className="theme-preview-canvas" style={{ "--preview-accent": active.color, "--preview-deep": active.deep, "--preview-font": `${size}px`, "--preview-radius": `${radius}px` } as CSSProperties}>
      <div className={`theme-preview-window ${sticky ? "is-sticky" : ""}`}>
        <div className="theme-window-top"><span /><span /><span /><i>your trade portal</i></div>
        {topbar && <div className="theme-window-announcement">New smart home products launched <b>Read the announcement →</b></div>}
        <div className="theme-window-header">
          <div className="theme-window-brand"><span>⌂</span><strong>your brand<small>PARTNER PORTAL</small></strong></div>
          <div className="theme-window-actions">{search && <span><Search size={13} /> Search</span>}{account && <span><UserRound size={13} /> Account</span>}</div>
        </div>
        <div className="theme-window-nav">Home <span>Products</span> <span>Technical</span> <span>Marketing</span> <span>Training</span></div>
        <div className="theme-window-body">
          <div className="theme-window-hero"><small>YOUR PARTNER PORTAL</small><strong>Better together.<br />Smarter living.</strong><span>Explore products →</span></div>
          <div className="theme-window-cards"><div><i>▣</i><strong>Products</strong></div><div><i>▤</i><strong>Technical</strong></div><div><i>▧</i><strong>Marketing</strong></div></div>
          <div className="theme-window-product">
            <span className="theme-window-product-image" />
            <div><small>CONTROLLERS</small><strong>Connect Hub</strong><p>{catalogue ? "Product details only" : "$199.00"}</p>
              <em className={catalogue ? "is-catalogue" : ""}>{catalogue ? "Ask about this product →" : "Add to cart"}</em>
            </div>
          </div>
        </div>
        <div className="theme-window-footer">
          <div><b>your brand</b>{contact && <span>+44 20 7946 0000<br />partners@yourcompany.com<br />Mon–Fri 9am–5pm</span>}</div>
          <div>© 2026 your brand</div>
        </div>
      </div>
    </div>
  </div>;
}
