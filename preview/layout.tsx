import type { Metadata, Viewport } from "next";
import type { ReactNode } from "react";
import SiteHeader from "@/components/site-header";
import SiteFooter from "@/components/site-footer";
import { CompareTray } from "@/components/compare";
import "./globals.css";

export const metadata: Metadata = {
  title: { default: "Aqara | Trade Partner Portal", template: "%s" },
  description: "Your partner hub for Aqara smart home products, technical resources, firmware, marketing and training.",
  robots: { index: false, follow: false }, // B2B portal: keep out of search engines by default
};

export const viewport: Viewport = { themeColor: "#0E3B33", width: "device-width", initialScale: 1 };

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body>
        <SiteHeader />
        <div id="main-content" tabIndex={-1}>{children}</div>
        <SiteFooter />
        <CompareTray />
      </body>
    </html>
  );
}
