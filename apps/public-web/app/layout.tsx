import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'KABEERI Public Runtime',
  description: 'Next.js public web runtime scaffold for KABEERI themes, audiences, Marketplace, and Mall separation.',
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="ar" dir="rtl">
      <body>{children}</body>
    </html>
  );
}