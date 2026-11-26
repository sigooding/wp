"use client";

import Link from "next/link";
import Image from "next/image";
import { useEffect, useMemo, useState } from "react";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { ArrowRight, ArrowUpRight, CheckCircle2, Search, X } from "lucide-react";
import { products, resources, type Resource } from "@/lib/content";
import { compatibilityFilters, protocolFilters, supportTopics } from "@/lib/b2b";
import { ResourceCard } from "./portal-parts";
import { CompareToggle } from "./compare";

const categories = ["All products", "Hubs", "Sensors", "Switches", "Climate"];

/** Only accept filter values that exist, so shared links can't break the page. */
function allowed(value: string | null, options: readonly string[]) {
  return value && options.includes(value) ? value : "";
}

/** Filters live in the URL (?category, ?protocol, ?compat, ?q) so a filtered view can be shared. */
export function ProductCatalogue() {
  const router = useRouter();
  const pathname = usePathname();
  const sp = useSearchParams();
  const [category, setCategory] = useState(allowed(sp.get("category"), categories) || "All products");
  const [protocol, setProtocol] = useState(allowed(sp.get("protocol"), protocolFilters));
  const [compat, setCompat] = useState(allowed(sp.get("compat"), compatibilityFilters));
  const [query, setQuery] = useState(sp.get("q") || "");

  useEffect(() => {
    const params = new URLSearchParams();
    if (category !== "All products") params.set("category", category);
    if (protocol) params.set("protocol", protocol);
    if (compat) params.set("compat", compat);
    if (query.trim()) params.set("q", query.trim());
    const next = params.toString() ? `${pathname}?${params}` : pathname;
    const timer = setTimeout(() => router.replace(next, { scroll: false }), 200);
    return () => clearTimeout(timer);
  }, [category, protocol, compat, query, pathname, router]);

  const matches = useMemo(() => {
    const q = query.toLowerCase().trim();
    return products.filter(
      (p) =>
        (category === "All products" || p.category === category) &&
        (!protocol || p.protocols.includes(protocol)) &&
        (!compat || p.compatibility.some((c) => c.includes(compat))) &&
        `${p.name} ${p.category} ${p.description} ${p.sku} ${p.protocols.join(" ")}`.toLowerCase().includes(q),
    );
  }, [category, protocol, compat, query]);

  const counts = useMemo(
    () => Object.fromEntries(categories.map((c) => [c, c === "All products" ? products.length : products.filter((p) => p.category === c).length])),
    [],
  );

  const filtersActive = category !== "All products" || Boolean(protocol) || Boolean(compat) || Boolean(query.trim());
  const reset = () => { setCategory("All products"); setProtocol(""); setCompat(""); setQuery(""); };

  return (
    <>
      <div className="section-toolbar catalogue-toolbar">
        <div className="filter-list" role="group" aria-label="Product categories">
          {categories.map((item) => (
            <button key={item} type="button" className={`filter-chip ${item === category ? "selected" : ""}`} aria-pressed={item === category} onClick={() => setCategory(item)}>
              {item} <em>{counts[item]}</em>
            </button>
          ))}
        </div>
        <div className="filter-selects">
          <label className="select-field"><span>Protocol</span>
            <select value={protocol} onChange={(e) => setProtocol(e.target.value)}>
              <option value="">Any protocol</option>
              {protocolFilters.map((p) => <option key={p}>{p}</option>)}
            </select>
          </label>
          <label className="select-field"><span>Works with</span>
            <select value={compat} onChange={(e) => setCompat(e.target.value)}>
              <option value="">Any ecosystem</option>
              {compatibilityFilters.map((c) => <option key={c}>{c}</option>)}
            </select>
          </label>
          <label className="inline-search"><Search size={16} aria-hidden="true" />
            <input placeholder="Name, SKU or protocol" value={query} onChange={(e) => setQuery(e.target.value)} aria-label="Search products" />
            {query && <button type="button" className="clear-search" onClick={() => setQuery("")} aria-label="Clear search"><X size={14} /></button>}
          </label>
        </div>
      </div>
      <div className="result-row">
        <p className="result-count" aria-live="polite">
          Showing {matches.length} {matches.length === 1 ? "product" : "products"}
          {category !== "All products" && <> in <strong>{category}</strong></>}
          {query.trim() && <> matching “{query.trim()}”</>}
        </p>
        {filtersActive && <button type="button" className="text-link" onClick={reset}>Reset filters</button>}
      </div>
      <div className="product-grid">
        {matches.length ? matches.map((product, i) => (
          <article className="product-card" key={product.slug}>
            <Link href={`/products/${product.slug}`} className="product-card-link" aria-label={`${product.name}, ${product.category}`}>
              <div className="product-image"><Image src={product.image} alt="" width={640} height={640} sizes="(max-width: 520px) 50vw, (max-width: 1100px) 33vw, 25vw" priority={i < 4} /><span>{product.badge}</span></div>
              <div className="product-card-content">
                <span>{product.category.toUpperCase()} · {product.sku}</span>
                <h3>{product.name}</h3>
                <p>{product.description}</p>
                <ul className="card-protocols" aria-label="Protocols">
                  {product.protocols.slice(0, 3).map((p) => <li key={p}>{p}</li>)}
                  {product.protocols.length > 3 && <li>+{product.protocols.length - 3}</li>}
                </ul>
              </div>
            </Link>
            <div className="product-card-footer product-card-content">
              <Link href={`/products/${product.slug}`} className="card-view">View product <ArrowUpRight size={15} /></Link>
              <CompareToggle slug={product.slug} name={product.name} />
            </div>
          </article>
        )) : (
          <div className="empty-state"><strong>No products found</strong>Try another search or filter. <button type="button" className="text-link" onClick={reset}>Reset filters</button></div>
        )}
      </div>
    </>
  );
}

export function ResourceLibrary({ category }: { category?: Resource["category"] }) {
  const [filter, setFilter] = useState("All resources");
  const [query, setQuery] = useState("");
  const filters = category ? ["All resources"] : ["All resources", "Technical", "Marketing"];
  const matches = resources.filter((r) => (!category || r.category === category) && (filter === "All resources" || r.category === filter) && `${r.title} ${r.label} ${r.description}`.toLowerCase().includes(query.toLowerCase().trim()));
  return (
    <>
      <div className="section-toolbar">
        <div className="filter-list" role="group" aria-label="Resource types">
          {filters.map((item) => <button key={item} type="button" className={`filter-chip ${filter === item ? "selected" : ""}`} aria-pressed={filter === item} onClick={() => setFilter(item)}>{item}</button>)}
        </div>
        <label className="inline-search"><Search size={16} aria-hidden="true" /><input placeholder="Search resources" value={query} onChange={(e) => setQuery(e.target.value)} aria-label="Search resources" />{query && <button type="button" className="clear-search" onClick={() => setQuery("")} aria-label="Clear search"><X size={14} /></button>}</label>
      </div>
      <p className="result-count" aria-live="polite">Showing {matches.length} {matches.length === 1 ? "resource" : "resources"}</p>
      <div className="library-grid">{matches.length ? matches.map((r) => <ResourceCard resource={r} key={r.slug} />) : <div className="empty-state"><strong>No resources found</strong>Try a different search or filter.</div>}</div>
    </>
  );
}

export function SupportForm({ initialTopic = "", initialProduct = "" }: { initialTopic?: string; initialProduct?: string }) {
  const [status, setStatus] = useState<"idle" | "sending" | "success" | "error">("idle");
  const [reference, setReference] = useState("");
  const topics: readonly string[] = supportTopics;
  const defaultTopic = topics.includes(initialTopic) ? initialTopic : "";
  const defaultMessage = initialProduct ? `Hi, I’d like more information about ${initialProduct}.\n\n` : "";

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = event.currentTarget;
    setStatus("sending");
    try {
      const response = await fetch("/api/support", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(Object.fromEntries(new FormData(form).entries())) });
      const result = await response.json();
      if (!response.ok) throw new Error(result.error || "Unable to send request");
      setReference(String(result.reference));
      setStatus("success");
      form.reset();
    } catch {
      setStatus("error");
    }
  }

  return (
    <div className="form-card">
      <h2>Send us a message</h2>
      <p>Tell us what you need and we’ll point you in the right direction. Include the SKU or serial number if it relates to a device.</p>
      <div aria-live="polite">
        {status === "success" && <div className="form-success" role="status"><CheckCircle2 size={18} style={{ display: "inline", verticalAlign: "middle", marginRight: 8 }} />Your request has been saved. Reference: #{reference}. We’ll reply to the email you provided.</div>}
        {status === "error" && <div className="form-error" role="alert">Sorry, we couldn’t send your request. Please check your details and try again.</div>}
      </div>
      <form onSubmit={handleSubmit}>
        <div className="form-grid">
          <div className="form-field"><label htmlFor="support-name">Your name</label><input id="support-name" name="name" placeholder="Enter your name" maxLength={120} autoComplete="name" required /></div>
          <div className="form-field"><label htmlFor="support-email">Email address</label><input id="support-email" name="email" type="email" placeholder="you@company.com" maxLength={254} autoComplete="email" required /></div>
        </div>
        <div className="form-field"><label htmlFor="support-topic">How can we help?</label><select id="support-topic" name="topic" required defaultValue={defaultTopic}><option value="" disabled>Select a topic</option>{topics.map((t) => <option key={t}>{t}</option>)}</select></div>
        <div className="form-field"><label htmlFor="support-message">Message</label><textarea id="support-message" name="message" placeholder="Tell us a little more about your question..." maxLength={5000} required defaultValue={defaultMessage} /></div>
        <button type="submit" className="button button-dark" disabled={status === "sending"} aria-busy={status === "sending"}>{status === "sending" ? "Sending..." : "Send message"}<ArrowRight size={16} /></button>
        <p className="form-note">Your details are used only to respond to your request.</p>
      </form>
    </div>
  );
}
