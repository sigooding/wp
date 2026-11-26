"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useRef, useState } from "react";
import { ArrowRight, ChevronDown, CircleHelp, LogOut, Menu, Search, UserRound, X } from "lucide-react";
import { navItems } from "@/lib/content";

export function Brand({ light = false }: { light?: boolean }) {
  return (
    <Link href="/" className={`brand ${light ? "brand-light" : ""}`} aria-label="Aqara trade partner portal home">
      <span className="brand-symbol" aria-hidden="true">
        <svg viewBox="0 0 40 40" fill="none">
          <path d="M5.5 17.5 20 5l14.5 12.5v15H23.7v-9h-7.4v9H5.5v-15Z" stroke="currentColor" strokeWidth="2.5" strokeLinejoin="round" />
          <path d="M13 14.7 20 8.8l7 5.9" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" />
        </svg>
      </span>
      <span className="brand-name"><strong>aqara</strong><small>SMART LIVING</small></span>
    </Link>
  );
}

export default function SiteHeader() {
  const pathname = usePathname();
  const [accountOpen, setAccountOpen] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const accountRef = useRef<HTMLDivElement>(null);

  // Close menus on route change.
  useEffect(() => { setAccountOpen(false); setMenuOpen(false); }, [pathname]);

  // Close the account dropdown on outside click or Escape.
  useEffect(() => {
    if (!accountOpen) return;
    const onPointer = (e: MouseEvent | TouchEvent) => { if (accountRef.current && !accountRef.current.contains(e.target as Node)) setAccountOpen(false); };
    const onKey = (e: KeyboardEvent) => { if (e.key === "Escape") setAccountOpen(false); };
    document.addEventListener("mousedown", onPointer);
    document.addEventListener("touchstart", onPointer);
    document.addEventListener("keydown", onKey);
    return () => { document.removeEventListener("mousedown", onPointer); document.removeEventListener("touchstart", onPointer); document.removeEventListener("keydown", onKey); };
  }, [accountOpen]);

  // Escape closes the mobile nav; lock body scroll while open.
  useEffect(() => {
    if (!menuOpen) return;
    const onKey = (e: KeyboardEvent) => { if (e.key === "Escape") setMenuOpen(false); };
    document.addEventListener("keydown", onKey);
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    return () => { document.removeEventListener("keydown", onKey); document.body.style.overflow = prev; };
  }, [menuOpen]);

  return (
    <header className="site-header">
      <a href="#main-content" className="skip-link">Skip to main content</a>
      <div className="shell header-top">
        <div className="header-brand-group">
          <Brand />
          <div className="header-divider" />
          <div className="portal-name">TRADE PARTNER<br /><strong>PORTAL</strong></div>
        </div>
        <div className="header-actions">
          <form action="/search" className="header-search" role="search">
            <Search size={17} aria-hidden="true" />
            <input type="search" name="q" placeholder="Search the portal..." aria-label="Search the portal" required />
            <button type="submit" aria-label="Submit search"><ArrowRight size={15} /></button>
          </form>
          <Link className="header-help" href="/support">
            <span className="header-round-icon"><CircleHelp size={18} /></span>
            <span><small>NEED A HAND?</small><strong>Partner support</strong></span>
          </Link>
          <div className="account-wrap" ref={accountRef}>
            <button className="account-button" onClick={() => setAccountOpen(!accountOpen)} aria-expanded={accountOpen} aria-haspopup="menu" aria-controls="account-menu" aria-label="Open account menu">
              <span className="account-avatar"><UserRound size={18} /></span>
              <span className="account-label"><small>YOUR PORTAL</small><strong>My Account</strong></span>
              <ChevronDown size={14} className={accountOpen ? "chevron-open" : ""} />
            </button>
            {accountOpen && (
              <div className="account-menu" id="account-menu" role="menu">
                <p>TRADE PARTNER PORTAL</p>
                <Link role="menuitem" href="/account" onClick={() => setAccountOpen(false)}><UserRound size={16} /> My account</Link>
                <Link role="menuitem" href="/login" onClick={() => setAccountOpen(false)}><LogOut size={16} /> Log out</Link>
              </div>
            )}
          </div>
          <button className="mobile-menu-button" aria-label={menuOpen ? "Close navigation" : "Open navigation"} aria-expanded={menuOpen} aria-controls="primary-navigation" onClick={() => setMenuOpen(!menuOpen)}>
            {menuOpen ? <X size={23} /> : <Menu size={23} />}
          </button>
        </div>
      </div>
      <div className={`navigation-bar ${menuOpen ? "navigation-open" : ""}`} id="primary-navigation">
        <div className="shell nav-inner">
          <nav className="main-nav" aria-label="Main navigation">
            {navItems.map((item) => {
              const active = item.href === "/" ? pathname === "/" : pathname === item.href || pathname.startsWith(`${item.href}/`);
              return <Link key={item.href} href={item.href} className={active ? "active" : ""} aria-current={active ? "page" : undefined} onClick={() => setMenuOpen(false)}>{item.label}</Link>;
            })}
            <Link className="mobile-account-link" href="/account" onClick={() => setMenuOpen(false)}>My Account</Link>
            <Link className="mobile-account-link" href="/login" onClick={() => setMenuOpen(false)}>Log out</Link>
          </nav>
          <div className="nav-note"><span /> YOUR PARTNER IN SMARTER LIVING</div>
          <form action="/search" className="mobile-search" role="search"><Search size={17} aria-hidden="true" /><input name="q" placeholder="Search the portal..." aria-label="Search the portal" required /><button type="submit" aria-label="Submit search"><ArrowRight size={16} /></button></form>
        </div>
      </div>
    </header>
  );
}
