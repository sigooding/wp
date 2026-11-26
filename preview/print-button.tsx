"use client";

import { Printer } from "lucide-react";

export function PrintButton({ label = "Print product sheet" }: { label?: string }) {
  return (
    <button type="button" className="button button-outline no-print" onClick={() => window.print()}>
      <Printer size={15} aria-hidden="true" /> {label}
    </button>
  );
}
