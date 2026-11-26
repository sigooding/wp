import Link from "next/link";
import { ArrowDownToLine, ArrowRight, ArrowUpRight, FileText } from "lucide-react";
import type { Resource } from "@/lib/content";

export function SectionHeading({ eyebrow, title, description, link, linkLabel }: { eyebrow: string; title: string; description?: string; link?: string; linkLabel?: string }) {
  return <div className="section-heading"><div><p className="eyebrow dark-eyebrow"><span />{eyebrow}</p><h2>{title}</h2>{description && <p className="section-description">{description}</p>}</div>{link && <Link href={link} className="text-link">{linkLabel || "View all"}<ArrowRight size={16} /></Link>}</div>;
}

export function ResourceCover({ resource, large = false }: { resource: Resource; large?: boolean }) {
  return <div className={`resource-cover ${large ? "resource-cover-large" : ""}`} style={{ backgroundImage: `linear-gradient(180deg, rgba(13,35,30,.12), rgba(11,30,26,.78)), url('${resource.image}')` }} aria-hidden="true"><span>AQARA</span><strong>{resource.coverTitle}</strong><small>SMART LIVING</small></div>;
}

export function ResourceRow({ resource }: { resource: Resource }) {
  return <div className="resource-row"><ResourceCover resource={resource} /><div className="resource-copy"><span>{resource.label}</span><h3>{resource.title}</h3><p>PDF document <i>·</i> {resource.updated}</p></div><a href={`/api/resources/${resource.slug}/download`} className="download-icon" aria-label={`Download ${resource.title}`} title={`Download ${resource.title}`}><ArrowDownToLine size={18} /></a></div>;
}

export function ResourceCard({ resource }: { resource: Resource }) {
  return <article className="library-card"><ResourceCover resource={resource} large /><div className="library-card-content"><div className="library-meta"><span>{resource.label}</span><span>PDF</span></div><h3>{resource.title}</h3><p>{resource.description}</p><div className="library-card-footer"><span><FileText size={14} /> {resource.updated}</span><a href={`/api/resources/${resource.slug}/download`}>Download <ArrowDownToLine size={15} /></a></div></div></article>;
}

export function PageIntro({ eyebrow, title, description }: { eyebrow: string; title: string; description: string }) {
  return <div className="page-intro"><div className="breadcrumbs"><Link href="/">Home</Link><span>/</span><span>{title}</span></div><p className="eyebrow dark-eyebrow"><span />{eyebrow}</p><h1>{title}</h1><p>{description}</p></div>;
}

export function MiniLink({ href, children }: { href: string; children: React.ReactNode }) {
  return <Link className="mini-link" href={href}>{children}<ArrowUpRight size={15} /></Link>;
}
