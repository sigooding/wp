/**
 * Distributor-portal data: support topics, firmware release notes and installer FAQs.
 * All entries are SAMPLE content for the preview. Replace with official Aqara material.
 * No pricing, ordering or customer-account data lives here, by design.
 */

export const supportTopics = [
  "Product information",
  "Technical support",
  "Firmware & software",
  "Warranty & returns",
  "Marketing resources",
  "Training",
  "Portal access",
  "Other",
] as const;

/** Max products in a comparison. Lives here (not in a client module) so server pages can import it. */
export const COMPARE_MAX = 4;

export const protocolFilters = ["Zigbee 3.0", "Thread", "Matter", "Wi-Fi", "Bluetooth", "Infrared"] as const;

export const compatibilityFilters = ["Apple Home", "Google Home", "Amazon Alexa", "Matter"] as const;

export type ReleaseKind = "Feature" | "Fix" | "Security";

export type Release = {
  product: string; // product slug
  version: string;
  date: string; // ISO yyyy-mm-dd
  kind: ReleaseKind;
  title: string;
  notes: string[];
};

export const firmwareReleases: Release[] = [
  { product: "hub-m3", version: "2.1.0", date: "2026-02-10", kind: "Feature", title: "Matter and Thread border router improvements", notes: ["More stable Thread mesh on busy networks", "Faster Matter commissioning from the Aqara app", "Sample notes: replace with official release notes"] },
  { product: "hub-m3", version: "2.0.4", date: "2026-01-14", kind: "Fix", title: "Automation timing fix", notes: ["Sensor-triggered scenes no longer lag after a reboot"] },
  { product: "motion-sensor-p1", version: "1.0.9", date: "2026-01-22", kind: "Fix", title: "Detection sensitivity calibration", notes: ["More consistent detection at the edge of the range"] },
  { product: "smart-wall-switch-h2", version: "1.4.2", date: "2025-12-03", kind: "Security", title: "Pairing security hardening", notes: ["Updated pairing security", "Recommended for all installed units"] },
  { product: "temperature-humidity-sensor-w100", version: "1.2.0", date: "2025-11-18", kind: "Feature", title: "E-ink refresh schedule", notes: ["Configurable display refresh interval"] },
];

export type Faq = { category: string; q: string; a: string };

export const faqs: Faq[] = [
  { category: "Setup", q: "Do Zigbee sensors need a hub?", a: "Yes. Zigbee devices connect through a compatible hub such as the Hub M3. Check each product’s compatibility list before planning an installation." },
  { category: "Setup", q: "Which ecosystems are supported?", a: "This varies by product. Matter and Thread products can work with Apple Home, Google Home and Amazon Alexa through a compatible hub. Confirm the exact combination on the product page." },
  { category: "Installation", q: "Can the H2 switch be fitted without a neutral wire?", a: "Some H2 variants are designed for no-neutral installations. Confirm the model and regional variant before installing." },
  { category: "Installation", q: "Where should motion sensors be mounted?", a: "Keep them away from direct heat sources and moving objects such as fans or curtains, at the height in the quick start guide." },
  { category: "Firmware", q: "How do I update device firmware?", a: "Updates are delivered through the Aqara app when a device is online. Check Firmware & updates for release notes, or request a firmware file from support." },
  { category: "Warranty & returns", q: "How do warranty claims work?", a: "Contact partner support with the SKU, serial number and a short description of the fault. We’ll confirm the next steps." },
];
