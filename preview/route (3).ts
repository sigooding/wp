import { db } from "@/db";
import { supportRequests } from "@/db/schema";
import { supportTopics } from "@/lib/b2b";

export const dynamic = "force-dynamic";

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const name = typeof body.name === "string" ? body.name.trim() : "";
    const email = typeof body.email === "string" ? body.email.trim() : "";
    const topic = typeof body.topic === "string" ? body.topic.trim() : "";
    const message = typeof body.message === "string" ? body.message.trim() : "";
    const topics: readonly string[] = supportTopics;
    if (!name || name.length > 120 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 254 || !topics.includes(topic) || !message || message.length > 5000) {
      return Response.json({ error: "Please provide valid details in every field." }, { status: 400 });
    }
    const [saved] = await db.insert(supportRequests).values({ name, email, topic, message }).returning({ id: supportRequests.id });
    return Response.json({ ok: true, reference: saved.id }, { status: 201 });
  } catch (error) {
    console.error("Unable to save support request", error);
    return Response.json({ error: "Unable to save your request." }, { status: 500 });
  }
}
