import { readFile } from "node:fs/promises";
import path from "node:path";

export const dynamic = "force-dynamic";

export async function GET() {
  try {
    const data = await readFile(path.join(process.cwd(), "public", "downloads", "aqara-partner-resources.zip"));
    return new Response(new Uint8Array(data), {
      headers: {
        "Content-Type": "application/zip",
        "Content-Disposition": 'attachment; filename="aqara-partner-resources.zip"',
        "Content-Length": String(data.length),
        "Cache-Control": "no-store",
      },
    });
  } catch {
    return new Response("Plugin package not found", { status: 404 });
  }
}
