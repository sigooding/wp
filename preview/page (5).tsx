import Link from "next/link";
import Image from "next/image";
import type { Metadata } from "next";
import { ArrowRight } from "lucide-react";
import { products, type Product } from "@/lib/content";
import { PrintButton } from "@/components/print-button";
import { COMPARE_MAX } from "@/lib/b2b";

export const metadata: Metadata = { title: "Compare products | Aqara Trade Partner Portal" };

type Props = { searchParams: Promise<{ items?: string | string[] }> };

export default async function ComparePage({ searchParams }: Props) {
  const sp = await searchParams;
  const raw = Array.isArray(sp.items) ? sp.items.join(",") : sp.items || "";
  const slugs = [...new Set(raw.split(",").map((s) => s.trim()).filter(Boolean))].slice(0, COMPARE_MAX);
  const picked = slugs.map((s) => products.find((p) => p.slug === s)).filter((p): p is Product => Boolean(p));

  if (picked.length === 0) {
    return (
      <main className="shell page-main">
        <div className="account-card">
          <p className="eyebrow dark-eyebrow"><span />COMPARE PRODUCTS</p>
          <h1 className="nf-title">Nothing to compare yet.</h1>
          <p>Choose up to {COMPARE_MAX} products from the catalogue, then select Compare.</p>
          <div className="detail-actions"><Link className="button button-dark" href="/products">Browse products <ArrowRight size={16} /></Link></div>
        </div>
      </main>
    );
  }

  const specLabels = [...new Set(picked.flatMap((p) => p.specs.map((s) => s.label)))];
  const rows: Array<[string, (p: Product) => string]> = [
    ["SKU", (p) => p.sku],
    ["Category", (p) => p.category],
    ["Protocols", (p) => p.protocols.join(", ")],
    ["Works with", (p) => p.compatibility.join(", ")],
    ...specLabels.map((label): [string, (p: Product) => string] => [label, (p) => p.specs.find((s) => s.label === label)?.value ?? "—"]),
    ["In the box", (p) => p.inBox.join(", ")],
  ];

  const removeUrl = (slug: string) => {
    const rest = picked.filter((p) => p.slug !== slug).map((p) => p.slug);
    return rest.length ? `/compare?items=${rest.join(",")}` : "/compare";
  };

  return (
    <main className="shell page-main">
      <nav className="breadcrumbs no-print" aria-label="Breadcrumb"><Link href="/">Home</Link><span>/</span><Link href="/products">Products</Link><span>/</span><span aria-current="page">Compare</span></nav>
      <p className="eyebrow dark-eyebrow"><span />SIDE BY SIDE</p>
      <h1 className="nf-title">Compare products</h1>
      <div className="compare-actions no-print">
        <Link className="button button-outline" href="/products">Add more products</Link>
        <PrintButton label="Print comparison" />
      </div>

      <div className="compare-scroll">
        <table className="compare-table">
          <caption className="sr-only">Comparison of {picked.map((p) => p.name).join(", ")}</caption>
          <thead>
            <tr>
              <th scope="col">Feature</th>
              {picked.map((p) => (
                <th scope="col" key={p.slug}>
                  <Image src={p.image} alt="" width={320} height={320} sizes="200px" />
                  <Link href={`/products/${p.slug}`}>{p.name}</Link>
                  <Link className="compare-remove no-print" href={removeUrl(p.slug)} aria-label={`Remove ${p.name} from comparison`}>Remove</Link>
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {rows.map(([label, value]) => (
              <tr key={label}>
                <th scope="row">{label}</th>
                {picked.map((p) => <td key={p.slug}>{value(p)}</td>)}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="preview-note">Sample comparison data. Replace with official Aqara specifications before publishing.</div>
    </main>
  );
}
