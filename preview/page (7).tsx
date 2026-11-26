import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowRight } from "lucide-react";
import { announcements } from "@/lib/content";

export default async function NewsArticle({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const article = announcements.find((item) => item.slug === slug);
  if (!article) notFound();
  const destination = slug === "new-smart-home-products-launched" ? "/products" : slug === "updated-installation-documentation" ? "/technical" : "/support";
  const label = slug === "new-smart-home-products-launched" ? "Explore products" : slug === "updated-installation-documentation" ? "View technical documents" : "Contact support";
  return <main className="shell page-main"><div className="breadcrumbs"><Link href="/">Home</Link><span>/</span><Link href="/news">News</Link><span>/</span><span>{article.title}</span></div><article className="article-card"><p className="eyebrow dark-eyebrow"><span />{article.category}</p><h1>{article.title}</h1><p className="article-date">{article.date} &nbsp; · &nbsp; Aqara partner news</p><p>{article.body}</p><p>Everything you need is right here in the partner portal, from product details and useful downloads to training and support.</p><Link className="button button-dark" href={destination}>{label} <ArrowRight size={16} /></Link><div className="preview-note">This is an example announcement for the portal preview. Replace it with your own news before publication.</div></article></main>;
}
