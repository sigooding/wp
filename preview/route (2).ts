import { PDFDocument, StandardFonts, rgb } from "pdf-lib";
import { readFile } from "node:fs/promises";
import path from "node:path";
import { resources } from "@/lib/content";

export async function GET(_request: Request, { params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const resource = resources.find((item) => item.slug === slug);
  if (!resource) return new Response("Resource not found", { status: 404 });

  const pdf = await PDFDocument.create();
  pdf.setTitle(`${resource.title} | Aqara`);
  pdf.setAuthor("Aqara Trade Partner Portal");
  const page = pdf.addPage([595, 842]);
  const regular = await pdf.embedFont(StandardFonts.Helvetica);
  const bold = await pdf.embedFont(StandardFonts.HelveticaBold);
  const dark = rgb(0.10, 0.23, 0.18);
  const green = rgb(0.28, 0.48, 0.35);
  const grey = rgb(0.39, 0.46, 0.41);
  page.drawRectangle({ x: 0, y: 680, width: 595, height: 162, color: dark });
  page.drawText("AQARA  /  SMART LIVING", { x: 48, y: 795, size: 10, font: bold, color: rgb(0.77, 0.88, 0.80) });
  page.drawText(resource.label, { x: 48, y: 755, size: 9, font: bold, color: rgb(0.68, 0.83, 0.72) });
  page.drawText(resource.title, { x: 48, y: 718, size: 25, font: bold, color: rgb(1, 1, 1), maxWidth: 500 });
  page.drawText("PARTNER RESOURCE  |  PREVIEW EDITION", { x: 48, y: 650, size: 9, font: bold, color: green });
  page.drawText(resource.description, { x: 48, y: 625, size: 11, font: regular, color: grey, maxWidth: 495, lineHeight: 17 });
  try {
    const imageData = await readFile(path.join(process.cwd(), "public", resource.image.replace(/^\//, "")));
    const image = await pdf.embedJpg(imageData);
    const imageWidth = 499;
    const imageHeight = 210;
    const scale = Math.max(imageWidth / image.width, imageHeight / image.height);
    const scaledWidth = image.width * scale;
    const scaledHeight = image.height * scale;
    // Contain the image in a clean editorial frame without clipping outside the page.
    const contained = Math.min(imageWidth / image.width, imageHeight / image.height);
    page.drawRectangle({ x: 48, y: 383, width: imageWidth, height: imageHeight, color: rgb(0.92, 0.95, 0.92) });
    page.drawImage(image, { x: 48 + (imageWidth - image.width * contained) / 2, y: 383 + (imageHeight - image.height * contained) / 2, width: image.width * contained, height: image.height * contained });
    void scaledWidth; void scaledHeight;
  } catch { /* The PDF is still useful if an image is unavailable. */ }
  page.drawText("A smarter way to work together", { x: 48, y: 345, size: 15, font: bold, color: dark });
  const lines = resource.category === "Technical"
    ? ["Plan your project and review the relevant product information.", "Always follow the official manufacturer documentation and local regulations.", "Contact your support team for current specifications and installation guidance."]
    : ["Explore the connected home range and the resources available to partners.", "Use the portal to find product information, training and support.", "Replace this sample with your approved, up-to-date brand material."];
  lines.forEach((line, index) => page.drawText(`•  ${line}`, { x: 48, y: 317 - index * 22, size: 10, font: regular, color: grey, maxWidth: 500 }));
  page.drawRectangle({ x: 48, y: 155, width: 499, height: 60, color: rgb(0.93, 0.96, 0.93) });
  page.drawText("PREVIEW CONTENT", { x: 63, y: 194, size: 9, font: bold, color: green });
  page.drawText("This is a sample portal resource, not an official product document.", { x: 63, y: 176, size: 10, font: regular, color: grey });
  page.drawText("Replace it with your own approved materials before distribution.", { x: 63, y: 161, size: 10, font: regular, color: grey });
  page.drawLine({ start: { x: 48, y: 84 }, end: { x: 547, y: 84 }, thickness: 0.7, color: rgb(0.86, 0.90, 0.87) });
  page.drawText("AQARA  |  TRADE PARTNER PORTAL", { x: 48, y: 65, size: 8, font: bold, color: green });
  page.drawText("2026  /  01", { x: 495, y: 65, size: 8, font: regular, color: grey });
  const bytes = await pdf.save();
  return new Response(new Uint8Array(bytes), { headers: { "Content-Type": "application/pdf", "Content-Disposition": `attachment; filename="${slug}.pdf"`, "Cache-Control": "public, max-age=3600" } });
}
