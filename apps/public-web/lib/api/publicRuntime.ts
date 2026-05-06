import { kabeeriPublicFoundationManifest } from '../theme-manifest/kabeeriPublicFoundation';
import { runtimeRoutes } from '../navigation/routes';

export type PublicWebRuntimeManifest = {
  version: 'V15';
  contract: 'public-web.v1';
  runtime: Record<string, unknown>;
  rules: Record<string, string>;
  routes: Array<{ key: string; path: string; title: string; source: string }>;
  theme_manifest: Record<string, unknown>;
};

export function localPublicRuntimeManifest(): PublicWebRuntimeManifest {
  return {
    version: 'V15',
    contract: 'public-web.v1',
    runtime: {
      path: 'apps/public-web',
      framework: 'Next.js App Router',
      language: 'TypeScript',
      styling: 'Tailwind CSS 4 plus Kabeeri tokens',
    },
    rules: {
      runtime_boundary: 'Next.js renders public themes; Laravel exposes stable JSON contracts.',
      api_only: 'No Laravel model, Blade, or database access inside the public runtime.',
    },
    routes: runtimeRoutes.map((route) => ({
      key: route.key,
      path: route.path,
      title: route.label,
      source: route.sourceVersion,
    })),
    theme_manifest: kabeeriPublicFoundationManifest,
  };
}

export async function fetchPublicRuntimeManifest(baseUrl?: string): Promise<PublicWebRuntimeManifest> {
  const origin = baseUrl ?? process.env.NEXT_PUBLIC_KABEERI_API_BASE_URL;

  if (!origin) {
    return localPublicRuntimeManifest();
  }

  const response = await fetch(`${origin.replace(/\/$/, '')}/api/public-web/manifest`, {
    headers: { accept: 'application/json' },
    next: { revalidate: 300 },
  });

  if (!response.ok) {
    throw new Error(`Public runtime manifest request failed with ${response.status}`);
  }

  return (await response.json()) as PublicWebRuntimeManifest;
}