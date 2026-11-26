"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { GitCompareArrows, X } from "lucide-react";

const STORAGE_KEY = "aqara-compare";
const CHANGE_EVENT = "aqara-compare-change";
import { COMPARE_MAX } from "@/lib/b2b";

function readSelection(): string[] {
  try {
    const value = JSON.parse(window.localStorage.getItem(STORAGE_KEY) || "[]");
    return Array.isArray(value) ? value.filter((v): v is string => typeof v === "string").slice(0, COMPARE_MAX) : [];
  } catch {
    return [];
  }
}

function writeSelection(items: string[]) {
  try {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items.slice(0, COMPARE_MAX)));
  } catch {
    // Storage can be blocked (private mode); the feature degrades to no-op.
  }
  window.dispatchEvent(new Event(CHANGE_EVENT));
}

/** Shared comparison selection, kept in sync across components and browser tabs. */
export function useCompare() {
  const [items, setItems] = useState<string[]>([]);
  useEffect(() => {
    const sync = () => setItems(readSelection());
    sync();
    window.addEventListener(CHANGE_EVENT, sync);
    window.addEventListener("storage", sync);
    return () => {
      window.removeEventListener(CHANGE_EVENT, sync);
      window.removeEventListener("storage", sync);
    };
  }, []);
  return items;
}

export function CompareToggle({ slug, name }: { slug: string; name: string }) {
  const items = useCompare();
  const selected = items.includes(slug);
  const full = !selected && items.length >= COMPARE_MAX;
  return (
    <button
      type="button"
      className={`compare-toggle no-print ${selected ? "is-on" : ""}`}
      aria-pressed={selected}
      disabled={full}
      title={full ? `Compare up to ${COMPARE_MAX} products at a time` : undefined}
      aria-label={`${selected ? "Remove" : "Add"} ${name} ${selected ? "from" : "to"} comparison`}
      onClick={() => {
        const current = readSelection();
        writeSelection(selected ? current.filter((s) => s !== slug) : [...current, slug]);
      }}
    >
      <GitCompareArrows size={13} aria-hidden="true" /> {selected ? "In comparison" : "Compare"}
    </button>
  );
}

/** Sticky bar shown once at least one product is selected. */
export function CompareTray() {
  const items = useCompare();
  if (items.length === 0) return null;
  return (
    <div className="compare-tray no-print" role="region" aria-label="Product comparison">
      <span>{items.length} of {COMPARE_MAX} products selected</span>
      <Link href={`/compare?items=${items.map(encodeURIComponent).join(",")}`}>Compare products</Link>
      <button type="button" onClick={() => writeSelection([])} aria-label="Clear comparison selection">
        <X size={12} aria-hidden="true" /> Clear
      </button>
    </div>
  );
}
