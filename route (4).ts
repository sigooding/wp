import { readFile } from "node:fs/promises";
import path from "node:path";

export const dynamic = "force-dynamic";

export async function GET() {
  try {
    const data = await readFile(path.join(process.cwd(), "public", "downloads", "nexora-trade-theme.zip"));
    return new Response(new Uint8Array(data), {
      headers: {
        "Content-Type": "application/zip",
        "Content-Disposition": 'attachment; filename="nexora-trade.zip"',
        "Content-Length": String(data.length),
        "Cache-Control": "no-store",
      },
    });
  } catch {
    return new Response("Theme package not found", { status: 404 });
  }
}
