"use client";

import { useEffect, useState } from "react";

/** Course checklist with progress remembered in this browser (no account needed). */
export function TrainingChecklist({ slug, steps }: { slug: string; steps: string[] }) {
  const storageKey = `aqara-training-${slug}`;
  const [done, setDone] = useState<boolean[]>(() => steps.map(() => false));

  useEffect(() => {
    try {
      const saved = JSON.parse(window.localStorage.getItem(storageKey) || "null");
      if (Array.isArray(saved) && saved.length === steps.length) setDone(saved.map(Boolean));
    } catch {
      // Ignore unreadable progress; start fresh.
    }
  }, [storageKey, steps.length]);

  const save = (next: boolean[]) => {
    setDone(next);
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(next));
    } catch {
      // Storage unavailable: progress lasts for this page view only.
    }
  };

  const count = done.filter(Boolean).length;
  const percent = steps.length ? Math.round((count / steps.length) * 100) : 0;

  return (
    <section className="spec-block" aria-labelledby="checklist-heading">
      <h3 id="checklist-heading">Your progress</h3>
      <div className="progress" role="progressbar" aria-label="Course progress" aria-valuemin={0} aria-valuemax={100} aria-valuenow={percent}>
        <span style={{ width: `${percent}%` }} />
      </div>
      <p className="result-count" aria-live="polite">{count} of {steps.length} complete{percent === 100 ? " · course finished" : ""}</p>
      <ul className="checklist">
        {steps.map((step, i) => (
          <li key={step}>
            <label>
              <input type="checkbox" checked={done[i] ?? false} onChange={() => save(done.map((v, idx) => (idx === i ? !v : v)))} />
              <span>{step}</span>
            </label>
          </li>
        ))}
      </ul>
      <button type="button" className="text-link" onClick={() => save(steps.map(() => false))}>Reset progress</button>
    </section>
  );
}
