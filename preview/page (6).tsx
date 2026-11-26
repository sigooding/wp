import Link from "next/link";
import type { Metadata } from "next";
import type { LucideIcon } from "lucide-react";
import { ArrowRight, ShieldAlert, Sparkles, Wrench } from "lucide-react";
import { products } from "@/lib/content";
import { firmwareReleases, type ReleaseKind } from "@/lib/b2b";
import { PageIntro } from "@/components/portal-parts";

export const metadata: Metadata = { title: "Firmware & updates | Aqara Trade Partner Portal" };

const kindIcon: Record<ReleaseKind, LucideIcon> = { Feature: Sparkles, Fix: Wrench, Security: ShieldAlert };

type Props = { searchParams: Promise<{ product?: string | string[] }> };

export default async function FirmwarePage({ searchParams }: Props) {
  const sp = await searchParams;
  const selected = (Array.isArray(sp.product) ? sp.product[0] : sp.product) || "";
  const nameOf = (slug: string) => products.find((p) => p.slug === slug)?.name ?? slug;
  const releases = firmwareReleases
    .filter((r) => !selected || r.product === selected)
    .sort((a, b) => b.date.localeCompare(a.date));
  const withReleases = products.filter((p) => firmwareReleases.some((r) => r.product === p.slug));

  return (
    <main className="shell page-main">
      <PageIntro eyebrow="SOFTWARE & UPDATES" title="Firmware & updates" description="Release notes for Aqara devices, so you can plan updates before visiting a site. Request a firmware file from support if you need one." />

      <div className="filter-list" role="group" aria-label="Filter by product">
        <Link href="/firmware" className={`filter-chip ${!selected ? "selected" : ""}`} aria-current={!selected ? "page" : undefined}>All products</Link>
        {withReleases.map((p) => (
          <Link key={p.slug} href={`/firmware?product=${p.slug}`} className={`filter-chip ${selected === p.slug ? "selected" : ""}`} aria-current={selected === p.slug ? "page" : undefined}>{p.name}</Link>
        ))}
      </div>

      {releases.length === 0 ? (
        <div className="empty-state" style={{ marginTop: 18 }}><strong>No releases for this product yet</strong>Request the latest file from support.</div>
      ) : (
        <ol className="release-list" aria-label="Release notes, newest first">
          {releases.map((r) => {
            const Icon = kindIcon[r.kind];
            const label = `${nameOf(r.product)} v${r.version}`;
            return (
              <li className="release-card" key={`${r.product}-${r.version}`}>
                <div className="release-head">
                  <span className={`kind kind-${r.kind.toLowerCase()}`}><Icon size={13} aria-hidden="true" />{r.kind}</span>
                  <time dateTime={r.date}>{new Date(`${r.date}T00:00:00Z`).toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric", timeZone: "UTC" })}</time>
                </div>
                <h2>{r.title}</h2>
                <p className="release-product">{label}</p>
                <ul>{r.notes.map((n) => <li key={n}>{n}</li>)}</ul>
                <div className="release-actions">
                  <Link href={`/support?topic=${encodeURIComponent("Firmware & software")}&product=${encodeURIComponent(label)}`}>Request firmware file <ArrowRight size={14} aria-hidden="true" /></Link>
                </div>
              </li>
            );
          })}
        </ol>
      )}

      <div className="preview-note">Sample release notes for the portal preview. Replace with official Aqara firmware notes before publishing.</div>
    </main>
  );
}
