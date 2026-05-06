export const kabeeriPublicFoundationManifest = {
  id: 'kabeeri-public-foundation',
  version: '0.1.0',
  runtime: 'next-app-router',
  defaultLocale: 'ar',
  defaultDirection: 'rtl',
  apiContracts: ['public-web.v1'],
  quality: ['rtl', 'responsive', 'accessible', 'api-only', 'marketplace-mall-separated'],
  components: [
    'Hero',
    'AudienceGrid',
    'OnboardingTimeline',
    'MarketplaceMallSplit',
    'DeveloperEconomy',
    'RuntimeBoundary',
    'OwnerConsole',
  ],
  sections: {
    public: ['story', 'audiences', 'onboarding', 'pricing'],
    developer: ['manifest', 'review', 'signing', 'revenue'],
    mall: ['trust', 'moderation', 'claim', 'lead-flow'],
    owner: ['route-parity', 'release-gates', 'manual-qa'],
  },
} as const;

export type KabeeriPublicFoundationManifest = typeof kabeeriPublicFoundationManifest;