import Link from "next/link";
import Image from "next/image";
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArrowRight } from "lucide-react";
import { training } from "@/lib/content";
import { TrainingChecklist } from "@/components/training-checklist";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const lesson = training.find((t) => t.slug === slug);
  return { title: lesson ? `${lesson.title} | Aqara Trade Partner Portal` : "Training | Aqara Trade Partner Portal" };
}

export default async function TrainingDetail({ params }: Props) {
  const { slug } = await params;
  const lesson = training.find((item) => item.slug === slug);
  if (!lesson) notFound();

  return (
    <main className="shell page-main">
      <nav className="breadcrumbs" aria-label="Breadcrumb"><Link href="/">Home</Link><span>/</span><Link href="/training">Training</Link><span>/</span><span aria-current="page">{lesson.title}</span></nav>
      <div className="detail-layout">
        <div className="detail-image"><Image src={lesson.image} alt="" width={1024} height={890} sizes="(max-width: 800px) 100vw, 50vw" priority /></div>
        <article className="detail-content">
          <p className="eyebrow dark-eyebrow"><span />{lesson.type} · {lesson.duration.toUpperCase()}</p>
          <h1>{lesson.title}</h1>
          <p>{lesson.description}</p>
          <TrainingChecklist slug={lesson.slug} steps={lesson.sections} />
          <div className="detail-actions">
            <Link href="/downloads" className="button button-dark">Browse resources <ArrowRight size={16} /></Link>
            <Link href={`/support?topic=${encodeURIComponent("Training")}`} className="button button-outline">Ask a training question <ArrowRight size={16} /></Link>
          </div>
          <div className="preview-note">Progress is saved in this browser only. Sample course outline; replace with your official training content.</div>
        </article>
      </div>
    </main>
  );
}
