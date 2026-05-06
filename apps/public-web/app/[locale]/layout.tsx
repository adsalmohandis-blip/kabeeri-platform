import { isSupportedLocale, type SupportedLocale } from '../../lib/navigation/routes';

export function generateStaticParams() {
  return [{ locale: 'ar' }, { locale: 'en' }];
}

export default async function LocaleLayout({
  children,
  params,
}: Readonly<{
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}>) {
  const { locale } = await params;
  const safeLocale: SupportedLocale = isSupportedLocale(locale) ? locale : 'ar';

  return (
    <div lang={safeLocale} dir={safeLocale === 'ar' ? 'rtl' : 'ltr'} data-locale={safeLocale}>
      {children}
    </div>
  );
}