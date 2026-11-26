import Link from "next/link";
import { ArrowRight, Search } from "lucide-react";

export const metadata = { title: "Page not found | Aqara Trade Partner Portal" };

export default function NotFound() {
  return <main className="shell page-main">
    <div className="account-card">
      <p className="eyebrow dark-eyebrow"><span />ERROR 404</p>
      <h1 className="nf-title">We couldn’t find that page.</h1>
      <p>The link may have changed. Try the portal navigation, or search below.</p>
      <div className="detail-actions"><Link className="button button-dark" href="/">Back to the portal <ArrowRight size={16} /></Link><Link className="button button-outline" href="/products">Browse products</Link><Link className="button button-outline" href="/support">Contact support</Link></div>
      <form action="/search" role="search" className="nf-search"><Search size={16} aria-hidden="true" /><input type="search" name="q" placeholder="Search the portal..." aria-label="Search the portal" required /><button type="submit" className="button button-dark">Search</button></form>
    </div>
  </main>;
}
