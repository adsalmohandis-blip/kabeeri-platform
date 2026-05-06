export const supportedLocales = ['ar', 'en'] as const;

export type SupportedLocale = (typeof supportedLocales)[number];

export type PublicRuntimeRoute = {
  key: string;
  path: string;
  label: string;
  audience: 'public' | 'developer' | 'mall' | 'owner';
  sourceVersion: 'V11' | 'V12' | 'V13' | 'V15';
};

export const runtimeRoutes: PublicRuntimeRoute[] = [
  { key: 'home', path: '/', label: 'الرئيسية', audience: 'public', sourceVersion: 'V15' },
  { key: 'audiences', path: '/for', label: 'اختر مسارك', audience: 'public', sourceVersion: 'V11' },
  { key: 'pricing', path: '/pricing', label: 'الاشتراكات', audience: 'public', sourceVersion: 'V11' },
  { key: 'marketplace', path: '/marketplace', label: 'Marketplace', audience: 'developer', sourceVersion: 'V12' },
  { key: 'developers', path: '/developers', label: 'المطورين', audience: 'developer', sourceVersion: 'V12' },
  { key: 'mall', path: '/mall', label: 'Mall', audience: 'mall', sourceVersion: 'V13' },
];

export const audienceCards = [
  {
    key: 'business',
    title: 'صاحب مشروع',
    text: 'ابدأ Kabeeri App واضح بدل فوضى الإضافات، ثم افتح التجارة وCRM والعمليات وظهور Mall تدريجيًا.',
    href: '/for',
  },
  {
    key: 'enterprise',
    title: 'شركة أو مؤسسة',
    text: 'حوكمة، صلاحيات، تدقيق، تكاملات، BI/GRC، ومسار اعتماد آمن بدل توسع عشوائي.',
    href: '/for',
  },
  {
    key: 'developer',
    title: 'مطور أو Creator',
    text: 'ابن ثيمات وبلجنز وConnectors بعقود Manifest ومراجعة وتوقيع وفرصة بيع داخل المنصة.',
    href: '/developers',
  },
  {
    key: 'partner',
    title: 'مسوق أو Partner',
    text: 'مسارات إحالة، حملات، storefront، تسليم leads إلى CRM، ونمو قابل للقياس.',
    href: '/for',
  },
] as const;

export function localizedPath(locale: SupportedLocale, path: string): string {
  return `/${locale}${path === '/' ? '' : path}`;
}

export function isSupportedLocale(locale: string): locale is SupportedLocale {
  return supportedLocales.includes(locale as SupportedLocale);
}