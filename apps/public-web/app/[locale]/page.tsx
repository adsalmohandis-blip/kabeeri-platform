import { PublicRuntimePage } from '../../components/sections/PublicRuntimePage';
import { isSupportedLocale, type SupportedLocale } from '../../lib/navigation/routes';

export default async function Page({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  const safeLocale: SupportedLocale = isSupportedLocale(locale) ? locale : 'ar';

  return <PublicRuntimePage locale={safeLocale} />;
}