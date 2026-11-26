import Link from "next/link";
import Image from "next/image";
import { Suspense } from "react";
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArrowRight, ArrowUpRight, CircleHelp, LogOut, UserRound } from "lucide-react";
import { PageIntro } from "@/components/portal-parts";
import { ProductCatalogue, ResourceLibrary, SupportForm } from "@/components/catalogue";
import { announcements, products, resources, training } from "@/lib/content";
import { faqs, firmwareReleases } from "@/lib/b2b";

type Props = {
  params: Promise<{ section: string }>;
  searchParams: Promise<{ q?: string | string[]; topic?: string | string[]; product?: string | string[] }>;
};

const sectionTitles: Record<string, string> = {
  products: "Products",
  technical: "Technical Documents",
  marketing: "Marketing Assets",
  training: "Training Centre",
  downloads: "Downloads",
  news: "News & updates",
  support: "Partner Support",
  account: "My Account",
  login: "Signed out",
  search: "Search",
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { section } = await params;
  const title = sectionTitles[section];
  return { title: title ? `${title} | Aqara Trade Partner Portal` : "Aqara Trade Partner Portal" };
}

function nameFor(slug: string) {
  return products.find((p) => p.slug === slug)?.name ?? slug;
}

function first(value: string | string[] | undefined) {
  return (Array.isArray(value) ? value[0] : value) || "";
}

export default async function SectionPage({ params, searchParams }: Props) {
  const { section } = await params;

  if (section === "products") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="EXPLORE THE RANGE" title="Products" description="Find the right Aqara device by protocol, ecosystem or category. Compare up to four products side by side." />
        <Suspense fallback={<p className="result-count">Loading products…</p>}><ProductCatalogue /></Suspense>
        <p className="preview-note">Sample products for the portal preview. Replace them with official Aqara product data before publishing.</p>
      </main>
    );
  }

  if (section === "technical") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="KNOWLEDGE & EXPERTISE" title="Technical Documents" description="Find the specifications, guides and practical resources to support every project." />
        <ResourceLibrary category="Technical" />
        <div className="release-teaser">
          <div><p className="eyebrow dark-eyebrow"><span />SOFTWARE & UPDATES</p><h2>Firmware & release notes</h2><p>Check what changed before you update a site.</p></div>
          <Link className="button button-dark" href="/firmware">View firmware <ArrowRight size={16} /></Link>
        </div>
      </main>
    );
  }

  if (section === "marketing") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="MADE TO SHARE" title="Marketing Assets" description="Explore brochures, brand materials and resources to help you tell the smart living story." />
        <ResourceLibrary category="Marketing" />
      </main>
    );
  }

  if (section === "downloads") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="YOUR RESOURCE LIBRARY" title="Downloads" description="All the useful information you need, together in one easy-to-find place." />
        <ResourceLibrary />
        <p className="preview-note">Downloads are sample PDF resources. Replace them with your own approved documents before sharing with partners.</p>
      </main>
    );
  }

  if (section === "training") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="GROW YOUR KNOWLEDGE" title="Training Centre" description="Learn about our products, connected living and the essentials of a successful installation. Your progress is saved in this browser." />
        <p className="result-count">Explore learning resources</p>
        <div className="training-grid">
          {training.map((item) => (
            <Link href={`/training/${item.slug}`} className="training-card" key={item.slug}>
              <Image className="training-image" src={item.image} alt="" width={1024} height={576} sizes="(max-width: 800px) 50vw, 33vw" />
              <div className="training-card-content"><span>{item.type}</span><h3>{item.title}</h3><p>{item.description}</p><div className="training-card-footer"><span>{item.duration}</span><strong>Start course <ArrowUpRight size={15} /></strong></div></div>
            </Link>
          ))}
        </div>
      </main>
    );
  }

  if (section === "news") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="THE LATEST FROM AQARA" title="News & updates" description="The latest product news, updates and announcements for our trade partners." />
        <div className="news-list">
          {announcements.map((item) => (
            <Link href={`/news/${item.slug}`} className="announcement-row" key={item.slug}>
              <div className="date-tile"><strong>{item.day}</strong><span>{item.month}</span></div>
              <div className="announcement-copy"><span>{item.category} &nbsp; · &nbsp; {item.date.toUpperCase()}</span><h3>{item.title}</h3><p>{item.excerpt}</p></div>
              <ArrowUpRight className="row-arrow" size={18} aria-hidden="true" />
            </Link>
          ))}
        </div>
      </main>
    );
  }

  if (section === "support") {
    const sp = await searchParams;
    const categoriesFaq = [...new Set(faqs.map((f) => f.category))];
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="WE’RE HERE FOR YOU" title="Partner Support" description="Have a question? Check the FAQ first, or send us a message and we’ll respond by email." />
        <div className="support-layout">
          <SupportForm initialTopic={first(sp.topic).slice(0, 60)} initialProduct={first(sp.product).slice(0, 120)} />
          <aside className="info-card">
            <span className="info-card-icon"><CircleHelp size={23} aria-hidden="true" /></span>
            <h2>Quick routes</h2>
            <p>Most questions are answered faster through these pages.</p>
            <Link className="mini-link" href="/technical">Technical documents <ArrowUpRight size={15} aria-hidden="true" /></Link>
            <Link className="mini-link" href="/firmware" style={{ marginTop: 14 }}>Firmware & updates <ArrowUpRight size={15} aria-hidden="true" /></Link>
            <Link className="mini-link" href="/training" style={{ marginTop: 14 }}>Training Centre <ArrowUpRight size={15} aria-hidden="true" /></Link>
            <Link className="mini-link" href="/products" style={{ marginTop: 14 }}>Compare products <ArrowUpRight size={15} aria-hidden="true" /></Link>
          </aside>
        </div>

        <section className="faq-section" id="faq" aria-labelledby="faq-heading">
          <h2 id="faq-heading" className="section-heading-plain">Installer FAQ</h2>
          <p className="section-description">Quick answers to common questions. Sample answers: replace with official Aqara guidance.</p>
          {categoriesFaq.map((cat) => (
            <div key={cat} className="faq-group">
              <h3>{cat}</h3>
              {faqs.filter((f) => f.category === cat).map((f) => (
                <details key={f.q} className="faq-item"><summary>{f.q}</summary><p>{f.a}</p></details>
              ))}
            </div>
          ))}
        </section>
      </main>
    );
  }

  if (section === "account") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="YOUR PORTAL" title="My Account" description="Your access point to the trade partner portal." />
        <div className="account-card">
          <span className="account-icon"><UserRound size={23} aria-hidden="true" /></span>
          <h2>Trade partner access</h2>
          <p>This preview does not contain customer accounts, order history or payment information. In your WordPress site, partner access can be managed by your site administrator.</p>
          <div className="detail-actions"><Link href="/support" className="button button-dark">Contact support <ArrowRight size={16} /></Link><Link href="/login" className="button button-outline">Log out <LogOut size={15} aria-hidden="true" /></Link></div>
          <div className="preview-note">Preview mode: access is not restricted. Add a membership or access-control solution to your WordPress site before publishing private content.</div>
        </div>
      </main>
    );
  }

  if (section === "login") {
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="TRADE PARTNER PORTAL" title="You’ve left the preview" description="Thank you for visiting the Aqara partner portal." />
        <div className="account-card">
          <span className="account-icon"><LogOut size={23} aria-hidden="true" /></span>
          <h2>See you again soon.</h2>
          <p>This is an open preview, so no real account session has been changed. Your WordPress installation can use its own partner login and logout.</p>
          <Link href="/" className="button button-dark">Return to portal preview <ArrowRight size={16} /></Link>
        </div>
      </main>
    );
  }

  if (section === "search") {
    const sp = await searchParams;
    const query = first(sp.q).trim().slice(0, 120);
    const q = query.toLowerCase();
    const results = q
      ? [
          ...products.filter((i) => `${i.name} ${i.description} ${i.category} ${i.sku} ${i.protocols.join(" ")}`.toLowerCase().includes(q)).map((i) => ({ title: i.name, description: i.description, type: "PRODUCT", href: `/products/${i.slug}` })),
          ...resources.filter((i) => `${i.title} ${i.description} ${i.label}`.toLowerCase().includes(q)).map((i) => ({ title: i.title, description: i.description, type: "DOWNLOAD", href: `/api/resources/${i.slug}/download` })),
          ...firmwareReleases.filter((r) => `firmware update release ${r.version} ${r.kind} ${r.title} ${r.product} ${nameFor(r.product)} ${r.notes.join(" ")}`.toLowerCase().includes(q)).map((r) => ({ title: `Firmware ${r.version}: ${r.title}`, description: r.notes[0] ?? "", type: "FIRMWARE", href: `/firmware?product=${r.product}` })),
          ...faqs.filter((f) => `${f.q} ${f.a}`.toLowerCase().includes(q)).map((f) => ({ title: f.q, description: f.a, type: "FAQ", href: "/support#faq" })),
          ...announcements.filter((i) => `${i.title} ${i.excerpt}`.toLowerCase().includes(q)).map((i) => ({ title: i.title, description: i.excerpt, type: "NEWS", href: `/news/${i.slug}` })),
          ...training.filter((i) => `${i.title} ${i.description}`.toLowerCase().includes(q)).map((i) => ({ title: i.title, description: i.description, type: "TRAINING", href: `/training/${i.slug}` })),
        ]
      : [];
    return (
      <main className="shell page-main">
        <PageIntro eyebrow="FIND WHAT YOU NEED" title="Search results" description={query ? `Results for “${query}”` : "Enter a search term in the search bar to explore the portal."} />
        <p className="result-count" aria-live="polite">{results.length} {results.length === 1 ? "result" : "results"} found</p>
        <div className="search-results">
          {results.length ? results.map((r) => (
            <a href={r.href} className="search-result" key={`${r.type}-${r.href}-${r.title}`}>
              <div><span>{r.type}</span><h3>{r.title}</h3><p>{r.description}</p></div>
              <ArrowUpRight size={18} aria-hidden="true" />
            </a>
          )) : <div className="empty-state"><strong>No results found</strong>Try a different search, or browse the portal sections.</div>}
        </div>
      </main>
    );
  }

  notFound();
}

