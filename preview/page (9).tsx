import Link from "next/link";
import Image from "next/image";
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArrowDownToLine, ArrowRight, ArrowUpRight, CheckCircle2, FileText, Package, Radio } from "lucide-react";
import { products, resources } from "@/lib/content";
import { firmwareReleases } from "@/lib/b2b";
import { CompareToggle } from "@/components/compare";
import { PrintButton } from "@/components/print-button";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const product = products.find((item) => item.slug === slug);
  return { title: product ? `${product.name} | Aqara Trade Partner Portal` : "Product | Aqara Trade Partner Portal", description: product?.description };
}

export default async function ProductPage({ params }: Props) {
  const { slug } = await params;
  const product = products.find((item) => item.slug === slug);
  if (!product) notFound();

  const docs = product.resources.map((s) => resources.find((r) => r.slug === s)).filter((r): r is NonNullable<typeof r> => Boolean(r));
  const latestRelease = firmwareReleases.filter((r) => r.product === product.slug).sort((a, b) => b.date.localeCompare(a.date))[0];
  const related = [
    ...products.filter((p) => p.slug !== product.slug && p.category === product.category),
    ...products.filter((p) => p.slug !== product.slug && p.category !== product.category),
  ].slice(0, 3);
  const inquiry = `/support?topic=${encodeURIComponent("Product information")}&product=${encodeURIComponent(`${product.name} (${product.sku})`)}`;

  return (
    <main className="shell page-main">
      <nav className="breadcrumbs" aria-label="Breadcrumb"><Link href="/">Home</Link><span>/</span><Link href="/products">Products</Link><span>/</span><span aria-current="page">{product.name}</span></nav>

      <div className="detail-layout">
        <div className="detail-sticky">
          <div className="detail-image"><Image src={product.image} alt={product.name} width={1024} height={1024} sizes="(max-width: 800px) 100vw, 50vw" priority /></div>
          <ul className="protocol-badges" aria-label="Supported protocols">{product.protocols.map((p) => <li key={p}><Radio size={12} aria-hidden="true" /> {p}</li>)}</ul>
        </div>

        <div className="detail-content">
          <p className="eyebrow dark-eyebrow"><span />{product.category.toUpperCase()} <i className="sku-pill">SKU {product.sku}</i></p>
          <h1>{product.name}</h1>
          <p>{product.overview}</p>
          <h3>Key benefits</h3>
          <ul className="feature-list">{product.features.map((feature) => <li key={feature}><CheckCircle2 size={16} aria-hidden="true" />{feature}</li>)}</ul>

          <div className="detail-actions no-print">
            <Link className="button button-dark" href={inquiry}>Ask about this product <ArrowRight size={16} /></Link>
            <CompareToggle slug={product.slug} name={product.name} />
            <PrintButton />
          </div>

          {latestRelease && (
            <p className="firmware-line">
              Latest firmware: <strong>v{latestRelease.version}</strong> ({latestRelease.kind.toLowerCase()}) · <Link href={`/firmware?product=${product.slug}`}>See release notes</Link>
            </p>
          )}

          <section className="spec-block" aria-labelledby="specs-heading">
            <h3 id="specs-heading">Specifications</h3>
            <dl className="spec-table">{product.specs.map((s) => <div key={s.label}><dt>{s.label}</dt><dd>{s.value}</dd></div>)}</dl>
          </section>

          <div className="spec-columns">
            <section aria-labelledby="compat-heading"><h3 id="compat-heading">Works with</h3><ul className="chip-list">{product.compatibility.map((c) => <li key={c}>{c}</li>)}</ul></section>
            <section aria-labelledby="box-heading"><h3 id="box-heading"><Package size={14} aria-hidden="true" /> In the box</h3><ul className="plain-list">{product.inBox.map((c) => <li key={c}>{c}</li>)}</ul></section>
          </div>

          {docs.length > 0 && (
            <section className="spec-block" aria-labelledby="docs-heading">
              <h3 id="docs-heading">Downloads for this product</h3>
              <ul className="doc-list">
                {docs.map((d) => (
                  <li key={d.slug}><FileText size={15} aria-hidden="true" /><span><strong>{d.title}</strong><small>{d.label} · {d.updated}</small></span><a href={`/api/resources/${d.slug}/download`} aria-label={`Download ${d.title}`}>Download <ArrowDownToLine size={14} aria-hidden="true" /></a></li>
                ))}
              </ul>
            </section>
          )}

          <div className="preview-note">Sample product data for the portal preview. Values marked “(sample)” are illustrative. Replace them with official Aqara specifications, SKUs and regional variants before publishing.</div>
        </div>
      </div>

      <section className="related-section" aria-labelledby="related-heading">
        <div className="section-heading"><div><p className="eyebrow dark-eyebrow"><span />YOU MAY ALSO NEED</p><h2 id="related-heading">Related products</h2></div><Link href="/products" className="text-link">All products <ArrowRight size={16} /></Link></div>
        <div className="product-grid related-grid">
          {related.map((p) => (
            <article className="product-card" key={p.slug}>
              <Link href={`/products/${p.slug}`} className="product-card-link" aria-label={`${p.name}, ${p.category}`}>
                <div className="product-image"><Image src={p.image} alt="" width={640} height={640} sizes="(max-width: 800px) 50vw, 25vw" /><span>{p.badge}</span></div>
                <div className="product-card-content"><span>{p.category.toUpperCase()}</span><h3>{p.name}</h3><p>{p.description}</p></div>
              </Link>
              <div className="product-card-footer product-card-content"><Link href={`/products/${p.slug}`} className="card-view">View product <ArrowUpRight size={15} /></Link><CompareToggle slug={p.slug} name={p.name} /></div>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}
