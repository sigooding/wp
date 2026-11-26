import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Brand } from "./site-header";

export default function SiteFooter() {
  return (
    <footer className="site-footer">
      <div className="shell footer-main">
        <div className="footer-about"><Brand light /><p>Making smarter living simpler, together.</p></div>
        <div className="footer-links"><strong>EXPLORE</strong><Link href="/products">Products</Link><Link href="/technical">Technical</Link><Link href="/marketing">Marketing</Link></div>
        <div className="footer-links"><strong>PARTNER RESOURCES</strong><Link href="/training">Training</Link><Link href="/downloads">Downloads</Link><Link href="/news">News & updates</Link><Link href="/firmware">Firmware & updates</Link><Link href="/compare">Compare products</Link><Link href="/theme">WordPress theme</Link></div>
        <div className="footer-contact"><strong>HERE TO HELP</strong><p>Questions? We’re here to support you.</p><Link href="/support">Contact partner support <ArrowUpRight size={15} /></Link></div>
      </div>
      <div className="shell footer-bottom"><span>© 2026 Aqara. Made for our trade partners.</span><span>SMARTER SPACES. BETTER LIVING.</span></div>
    </footer>
  );
}
