import Link from "next/link";
import { ArrowDownToLine, ArrowRight, ArrowUpRight, BookOpenText, Box, FileText, Headset, Images, Sparkles } from "lucide-react";
import { announcements, resources } from "@/lib/content";
import { ResourceRow, SectionHeading } from "@/components/portal-parts";

const quickLinks = [
  { title: "Explore Products", description: "Browse our complete product range", href: "/products", icon: Box, number: "01" },
  { title: "Technical Documents", description: "Find specifications and manuals", href: "/technical", icon: FileText, number: "02" },
  { title: "Marketing Assets", description: "Download images and brochures", href: "/marketing", icon: Images, number: "03" },
  { title: "Training Centre", description: "Learn about products and installation", href: "/training", icon: BookOpenText, number: "04" },
];

export default function HomePage() {
  return (
    <main className="shell home-main">
      <section className="hero">
        <div className="hero-content">
          <p className="hero-eyebrow"><span /> THE AQARA PARTNER PORTAL</p>
          <h1>Welcome to your<br /><span>Trade Partner Portal.</span></h1>
          <p className="hero-description">Everything you need to promote, distribute and support our smart home product range.</p>
          <div className="hero-actions"><Link className="button button-light" href="/products">Explore products <ArrowRight size={17} /></Link><Link className="button button-ghost" href="/downloads">Browse resources <ArrowRight size={17} /></Link></div>
        </div>
        <div className="hero-caption"><span className="hero-caption-icon"><Sparkles size={18} /></span><span><small>SMARTER SPACES</small><strong>Better living, together.</strong></span></div>
      </section>

      <section className="toolkit-section">
        <SectionHeading eyebrow="YOUR PARTNER TOOLKIT" title="Everything you need, all in one place." description="Quick access to the tools and resources that help you do more." link="/downloads" linkLabel="Explore all resources" />
        <div className="tool-grid">{quickLinks.map(({ title, description, href, icon: Icon, number }) => <Link href={href} className="tool-card" key={title}><div className="tool-card-top"><span className="tool-icon"><Icon size={22} strokeWidth={1.65} /></span><span className="tool-number">{number}</span></div><div className="tool-card-bottom"><div><h3>{title}</h3><p>{description}</p></div><span className="card-arrow"><ArrowUpRight size={17} /></span></div></Link>)}</div>
      </section>

      <div className="home-columns">
        <section className="updates-section">
          <SectionHeading eyebrow="THE LATEST" title="Latest announcements" description="Stay in the know with news from Aqara." link="/news" linkLabel="View all news" />
          <div className="updates-panel">{announcements.map((item) => <Link href={`/news/${item.slug}`} className="announcement-row" key={item.slug}><div className="date-tile"><strong>{item.day}</strong><span>{item.month}</span></div><div className="announcement-copy"><span>{item.category}</span><h3>{item.title}</h3><p>{item.excerpt}</p></div><ArrowUpRight className="row-arrow" size={17} /></Link>)}</div>
        </section>
        <section className="featured-section">
          <SectionHeading eyebrow="HAND-PICKED FOR YOU" title="Featured resources" description="The essentials, ready to download." link="/downloads" linkLabel="View all downloads" />
          <div className="resources-panel">{resources.slice(0, 3).map((resource) => <ResourceRow resource={resource} key={resource.slug} />)}</div>
        </section>
      </div>

      <section className="support-banner"><div className="support-icon"><Headset size={25} /></div><div><p className="eyebrow dark-eyebrow">WE’RE HERE FOR YOU</p><h2>Need a hand? Let’s talk.</h2><p>Whatever you’re working on, our team is here to help.</p></div><Link className="button button-dark" href="/support">Contact support <ArrowRight size={16} /></Link></section>
      <div className="home-bottom-note"><span><ArrowDownToLine size={15} /> Your resources, all in one place.</span><span>BUILT FOR BETTER PARTNERSHIPS</span></div>
    </main>
  );
}
