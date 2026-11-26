export type Spec = { label: string; value: string };

export type Product = {
  slug: string;
  name: string;
  category: string;
  description: string;
  image: string;
  badge: string;
  overview: string;
  features: string[];
  /** Sample model code shown to partners. Replace with official Aqara SKUs. */
  sku: string;
  protocols: string[];
  specs: Spec[];
  compatibility: string[];
  inBox: string[];
  resources: string[]; // resource slugs
};

export const products: Product[] = [
  {
    slug: "hub-m3",
    name: "Hub M3",
    category: "Hubs",
    description: "The multi-protocol hub that connects your Aqara devices.",
    image: "/images/product-hub.jpg",
    badge: "ZIGBEE · THREAD · IR",
    overview: "Bring Aqara devices together on one dependable hub. Zigbee, Thread, Matter and IR support in a single compact unit built for installers.",
    features: ["Zigbee, Thread, Matter and IR in one hub", "Local control and automations", "Built for trade installations"],
    sku: "HM3-G01",
    protocols: ["Zigbee 3.0", "Thread", "Matter", "Wi-Fi", "Bluetooth", "Infrared"],
    specs: [{ label: "Power input", value: "USB-C, 5V/2A" }, { label: "Connectivity", value: "Wi-Fi 2.4/5 GHz, Ethernet (PoE)" }, { label: "Zigbee devices", value: "Up to 127 (sample)" }, { label: "Operating temp", value: "0°C to 40°C" }, { label: "Dimensions", value: "90 × 90 × 36 mm (sample)" }],
    compatibility: ["Aqara Home", "Apple Home", "Google Home", "Amazon Alexa", "Matter"],
    inBox: ["Hub M3", "USB-C cable", "Quick start guide"],
    resources: ["product-catalogue-2026", "smart-home-installation-guide", "product-specification-overview"],
  },
  {
    slug: "door-window-sensor-p2",
    name: "Door & Window Sensor P2",
    category: "Sensors",
    description: "Know the status of every door and window.",
    image: "/images/product-module.jpg",
    badge: "SECURITY",
    overview: "A compact contact sensor for doors, windows and cabinets, with accurate open/close detection for security and automation scenes.",
    features: ["Accurate open/close detection", "Compact, discreet design", "Works with Aqara hubs and HomeKit"],
    sku: "DW-S03D",
    protocols: ["Thread", "Matter"],
    specs: [{ label: "Battery", value: "2 × AAA (sample)" }, { label: "Battery life", value: "Up to 2 years (sample)" }, { label: "Mounting", value: "Adhesive pad or screws" }, { label: "Operating temp", value: "-10°C to 50°C" }, { label: "Dimensions", value: "Main 62 × 24 × 15 mm (sample)" }],
    compatibility: ["Aqara Home", "Apple Home", "Google Home", "Amazon Alexa", "Matter"],
    inBox: ["Sensor", "Magnet", "Adhesive pads", "Quick start guide"],
    resources: ["smart-home-installation-guide", "product-specification-overview"],
  },
  {
    slug: "motion-sensor-p1",
    name: "Motion Sensor P1",
    category: "Sensors",
    description: "Detect movement with fast, reliable response.",
    image: "/images/product-module.jpg",
    badge: "360° COVERAGE",
    overview: "A high-sensitivity motion sensor with wide detection coverage, ideal for lighting automation, security alerts and energy-saving scenes.",
    features: ["Wide 360° detection angle", "Fast response time", "Configurable detection range"],
    sku: "MS-S02",
    protocols: ["Zigbee 3.0"],
    specs: [{ label: "Battery", value: "2 × CR2450 (sample)" }, { label: "Battery life", value: "Up to 5 years (sample)" }, { label: "Detection angle", value: "170° horizontal (sample)" }, { label: "Detection range", value: "2–7 m adjustable" }, { label: "Operating temp", value: "-10°C to 50°C" }],
    compatibility: ["Aqara Home", "Apple Home (via hub)", "Google Home", "Amazon Alexa"],
    inBox: ["Sensor", "Magnetic stand", "Adhesive pads", "Quick start guide"],
    resources: ["smart-home-installation-guide", "commissioning-checklist"],
  },
  {
    slug: "smart-wall-switch-h2",
    name: "Smart Wall Switch H2",
    category: "Switches",
    description: "Smart control with a familiar wall-switch feel.",
    image: "/images/product-panel.jpg",
    badge: "NO NEUTRAL OPTION",
    overview: "Replace existing wall switches with smart control. The H2 range fits standard back boxes and works with or without a neutral wire depending on the model.",
    features: ["Fits standard back boxes", "Neutral and no-neutral models", "Energy monitoring options"],
    sku: "WS-K07E",
    protocols: ["Zigbee 3.0"],
    specs: [{ label: "Rated voltage", value: "100–250 V AC (sample)" }, { label: "Max load", value: "10 A per gang (sample)" }, { label: "Neutral wire", value: "Required / No-neutral models" }, { label: "Back box", value: "EU/UK square fit (check regional variant)" }, { label: "Energy monitoring", value: "Neutral models only" }],
    compatibility: ["Aqara Home", "Apple Home", "Google Home", "Amazon Alexa"],
    inBox: ["Switch", "Mounting screws", "Quick start guide"],
    resources: ["smart-home-installation-guide", "commissioning-checklist", "product-specification-overview"],
  },
  {
    slug: "temperature-humidity-sensor-w100",
    name: "Temperature & Humidity Sensor W100",
    category: "Climate",
    description: "Keep an eye on comfort in every room.",
    image: "/images/product-panel.jpg",
    badge: "CLIMATE MONITORING",
    overview: "A precise temperature and humidity sensor with a clear E-ink display, perfect for homes, care settings, rentals and workspaces.",
    features: ["Accurate temperature and humidity readings", "Clear E-ink display", "Low-power Zigbee design"],
    sku: "TH-S04D",
    protocols: ["Zigbee 3.0", "Thread"],
    specs: [{ label: "Display", value: "E-ink" }, { label: "Battery", value: "2 × AA (sample)" }, { label: "Accuracy", value: "±0.3°C / ±3% RH (sample)" }, { label: "Operating temp", value: "-10°C to 50°C" }, { label: "Mounting", value: "Wall plate or desk stand" }],
    compatibility: ["Aqara Home", "Apple Home", "Google Home", "Amazon Alexa"],
    inBox: ["Sensor", "Wall plate", "Stand", "Quick start guide"],
    resources: ["product-specification-overview"],
  },
  {
    slug: "water-leak-sensor-t1",
    name: "Water Leak Sensor T1",
    category: "Sensors",
    description: "Get alerted before a small leak becomes a big problem.",
    image: "/images/product-module.jpg",
    badge: "LEAK ALERTS",
    overview: "A compact water leak sensor for kitchens, bathrooms, laundry areas and plant rooms, sending instant alerts through your Aqara hub.",
    features: ["Instant leak alerts", "IP67 water resistance", "Long battery life"],
    sku: "WL-S02D",
    protocols: ["Zigbee 3.0"],
    specs: [{ label: "Battery", value: "CR2032 (sample)" }, { label: "Battery life", value: "Up to 2 years (sample)" }, { label: "Water resistance", value: "IP67" }, { label: "Operating temp", value: "0°C to 50°C" }, { label: "Dimensions", value: "Ø50 × 15 mm (sample)" }],
    compatibility: ["Aqara Home", "Apple Home (via hub)", "Google Home", "Amazon Alexa"],
    inBox: ["Sensor", "Quick start guide"],
    resources: ["smart-home-installation-guide", "commissioning-checklist"],
  },
];

export type Resource = {
  slug: string;
  title: string;
  category: "Marketing" | "Technical" | "Training";
  label: string;
  description: string;
  image: string;
  updated: string;
  coverTitle: string;
};

export const resources: Resource[] = [
  {
    slug: "product-catalogue-2026",
    title: "Product catalogue 2026",
    category: "Marketing",
    label: "PRODUCT CATALOGUE",
    description: "A closer look at our complete smart home range.",
    image: "/images/product-hub.jpg",
    updated: "2026 edition",
    coverTitle: "THE PRODUCT\nCOLLECTION",
  },
  {
    slug: "smart-home-installation-guide",
    title: "Smart home installation guide",
    category: "Technical",
    label: "INSTALLATION GUIDE",
    description: "A practical starting point for your next installation.",
    image: "/images/installation-guide.jpg",
    updated: "Updated February 2026",
    coverTitle: "INSTALLATION\nGUIDE",
  },
  {
    slug: "latest-marketing-brochure",
    title: "Latest marketing brochure",
    category: "Marketing",
    label: "MARKETING BROCHURE",
    description: "Introduce your customers to smarter everyday living.",
    image: "/images/portal-hero.jpg",
    updated: "2026 edition",
    coverTitle: "LIVE A LITTLE\nSMARTER",
  },
  {
    slug: "product-specification-overview",
    title: "Product specification overview",
    category: "Technical",
    label: "SPECIFICATION SHEET",
    description: "An overview of the information to include in your product specifications.",
    image: "/images/product-panel.jpg",
    updated: "Updated January 2026",
    coverTitle: "PRODUCT\nSPECIFICATIONS",
  },
  {
    slug: "commissioning-checklist",
    title: "Commissioning checklist",
    category: "Technical",
    label: "CHECKLIST",
    description: "A simple reference for preparing a connected home project.",
    image: "/images/product-module.jpg",
    updated: "Updated January 2026",
    coverTitle: "PROJECT\nCHECKLIST",
  },
  {
    slug: "brand-and-image-guidelines",
    title: "Brand and image guidelines",
    category: "Marketing",
    label: "BRAND GUIDELINES",
    description: "A helpful guide to presenting the brand consistently.",
    image: "/images/portal-hero.jpg",
    updated: "2026 edition",
    coverTitle: "BRAND\nGUIDELINES",
  },
];

export type Announcement = {
  slug: string;
  title: string;
  category: string;
  date: string;
  day: string;
  month: string;
  excerpt: string;
  body: string;
};

export const announcements: Announcement[] = [
  {
    slug: "new-smart-home-products-launched",
    title: "New smart home products launched",
    category: "PRODUCT NEWS",
    date: "18 February 2026",
    day: "18",
    month: "FEB",
    excerpt: "Meet the latest additions to our connected home range.",
    body: "Discover a more connected way of living. Explore the latest additions to the range and find the resources you need to introduce them to your customers.",
  },
  {
    slug: "updated-installation-documentation",
    title: "Updated installation documentation",
    category: "TECHNICAL UPDATE",
    date: "05 February 2026",
    day: "05",
    month: "FEB",
    excerpt: "The latest guides are ready when you need them.",
    body: "Our installation resources have been refreshed to help you find the right information for your projects. Visit the technical library to see the latest available guides.",
  },
  {
    slug: "latest-firmware-available",
    title: "Latest firmware available",
    category: "PRODUCT UPDATE",
    date: "27 January 2026",
    day: "27",
    month: "JAN",
    excerpt: "Stay up to date with the latest product information.",
    body: "Keep your projects up to date with the latest product information. Get in touch with the support team for details about firmware and compatibility.",
  },
];

export const training = [
  {
    slug: "getting-started",
    title: "Getting started with smart home",
    type: "GETTING STARTED",
    duration: "15 min read",
    description: "Get to know the essentials of a connected home and the product range.",
    image: "/images/portal-hero.jpg",
    sections: ["Understand the connected home", "Explore the product range", "Find the right resources"],
  },
  {
    slug: "installation-essentials",
    title: "Installation essentials",
    type: "INSTALLATION",
    duration: "20 min read",
    description: "Prepare for a successful project with our installation essentials.",
    image: "/images/installation-guide.jpg",
    sections: ["Plan the project", "Review technical documentation", "Connect with support"],
  },
  {
    slug: "introducing-the-range",
    title: "Introducing the range",
    type: "PRODUCT KNOWLEDGE",
    duration: "10 min read",
    description: "Feel confident introducing the benefits of smart living to customers.",
    image: "/images/product-hub.jpg",
    sections: ["Start with customer needs", "Introduce the connected experience", "Share helpful materials"],
  },
];

export const navItems = [
  { label: "Home", href: "/" },
  { label: "Products", href: "/products" },
  { label: "Technical", href: "/technical" },
  { label: "Marketing", href: "/marketing" },
  { label: "Training", href: "/training" },
  { label: "Downloads", href: "/downloads" },
  { label: "News", href: "/news" },
  { label: "Support", href: "/support" },
];
