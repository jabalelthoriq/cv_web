// app/layout.tsx
import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "NEXUS.AI — Platform Analisis CV",
  description: "Analisis CV, rekomendasi pekerjaan, dan simulasi interview berbasis AI",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="id">
      <body>{children}</body>
    </html>
  );
}