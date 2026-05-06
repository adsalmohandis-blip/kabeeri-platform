import { ActionLink } from '../ui/ActionLink';
import { SurfaceCard } from '../ui/SurfaceCard';
import { audienceCards, localizedPath, runtimeRoutes, type SupportedLocale } from '../../lib/navigation/routes';
import { kabeeriPublicFoundationManifest } from '../../lib/theme-manifest/kabeeriPublicFoundation';

export type PublicRuntimePageProps = {
  locale: SupportedLocale;
};

const onboardingSteps = [
  ['01', 'اختيار المسار', 'الزائر يختار هل هو صاحب مشروع، مؤسسة، مطور، شريك، أو زائر Mall.'],
  ['02', 'إنشاء Workspace', 'ننشئ Organization ثم Kabeeri App ونربط Company عند الحاجة.'],
  ['03', 'اختيار الثيم والإضافات', 'الثيمات والبلجنز تظهر بعقود صلاحيات وتوافق ومراجعة، لا كأزرار عشوائية.'],
  ['04', 'النشر والنمو', 'المشروع ينتقل من موقع واضح إلى تجارة وCRM وعمليات وظهور موثوق في Mall.'],
] as const;

export function PublicRuntimePage({ locale }: PublicRuntimePageProps) {
  return (
    <main className="kbr-public-runtime" data-runtime="next-public-web" data-theme-id={kabeeriPublicFoundationManifest.id}>
      <header className="kbr-nav kbr-shell" aria-label="Kabeeri public runtime navigation">
        <LinkLogo locale={locale} />
        <nav>
          {runtimeRoutes.map((route) => (
            <a key={route.key} href={localizedPath(locale, route.path)}>
              {route.label}
            </a>
          ))}
        </nav>
      </header>

      <section className="kbr-hero kbr-shell">
        <p className="kbr-kicker">KABEERI V15 Public Runtime</p>
        <h1>واجهة عامة قابلة للتوسع للثيمات، بدون خلط منطق Laravel مع React.</h1>
        <p className="kbr-hero__lead">
          V15 يفتح مسار Next.js الحقيقي: Laravel يبقى العقل والبيانات والصلاحيات، وNext.js يبقى واجهة الجمهور والثيمات التجارية
          عبر Manifest وAPI contracts واضحة.
        </p>
        <div className="kbr-hero__actions">
          <ActionLink href={localizedPath(locale, '/for')}>ابدأ من مسار الجمهور</ActionLink>
          <ActionLink href={localizedPath(locale, '/developers')} tone="ghost">افهم مسار المطورين</ActionLink>
        </div>
      </section>

      <section className="kbr-section kbr-shell" aria-labelledby="audiences-title">
        <div className="kbr-section-title">
          <p className="kbr-kicker">Audience-led platform</p>
          <h2 id="audiences-title">كل جمهور له طريق واضح، مش منصة كبيرة مرمية في وشه.</h2>
        </div>
        <div className="kbr-grid" data-columns="4">
          {audienceCards.map((card) => (
            <SurfaceCard key={card.key} eyebrow={card.key} title={card.title} accent={card.key === 'developer' ? 'sky' : 'bronze'}>
              <p>{card.text}</p>
              <a className="kbr-inline" href={localizedPath(locale, card.href)}>افتح المسار</a>
            </SurfaceCard>
          ))}
        </div>
      </section>

      <section className="kbr-section kbr-shell" aria-labelledby="onboarding-title">
        <div className="kbr-section-title">
          <p className="kbr-kicker">Onboarding spine</p>
          <h2 id="onboarding-title">مسار Onboarding مفهوم من أول زيارة حتى أول نشر.</h2>
        </div>
        <ol className="kbr-timeline">
          {onboardingSteps.map(([step, title, text]) => (
            <li key={step}>
              <span>{step}</span>
              <h3>{title}</h3>
              <p>{text}</p>
            </li>
          ))}
        </ol>
      </section>

      <section className="kbr-section kbr-shell" aria-labelledby="split-title">
        <div className="kbr-split">
          <SurfaceCard eyebrow="Extension economy" title="Kabeeri Marketplace" accent="olive">
            <p>المكان الداخلي للثيمات، البلجنز، Connectors، AI skills، وقوالب الصناعات. كل Package له Manifest وصلاحيات وتوافق وتوقيع ومراجعة.</p>
          </SurfaceCard>
          <SurfaceCard eyebrow="Public discovery" title="Kabeeri Mall" accent="clay">
            <p>واجهة الجمهور لاكتشاف شركات ومنتجات وخدمات ومواهب وسفر بثقة ومراجعة وبلاغات وClaim flow. لا يتم خلطها مع install/update للإضافات.</p>
          </SurfaceCard>
        </div>
        <h2 id="split-title" className="kbr-visually-soft">Marketplace and Mall stay separate in copy, routes, and permissions.</h2>
      </section>

      <section className="kbr-section kbr-shell" aria-labelledby="developer-title">
        <div className="kbr-section-title">
          <p className="kbr-kicker">Developer economy</p>
          <h2 id="developer-title">المطور يبيع داخل المنصة لأن العقد واضح من البداية.</h2>
        </div>
        <div className="kbr-grid" data-columns="3">
          {['Manifest validation', 'Automated QA', 'Review and signing', 'Listing and support', 'Usage and revenue', 'Safe rollback'].map((item) => (
            <SurfaceCard key={item} title={item} accent="sky">
              <p>جزء من lifecycle الثيمات والبلجنز قبل النشر التجاري في Marketplace.</p>
            </SurfaceCard>
          ))}
        </div>
      </section>

      <section className="kbr-section kbr-shell" aria-labelledby="owner-title">
        <div className="kbr-owner-panel">
          <p className="kbr-kicker">Owner and admin handoff</p>
          <h2 id="owner-title">قسم المالك: تعرف ماذا يعمل الآن وماذا يبقى قبل الإنتاج.</h2>
          <p>
            هذا الـ scaffold لا يستبدل Blade اليوم. هو يثبت runtime جديد، API manifest، route manifest، design tokens، smoke tests، ومسار نقل تدريجي لصفحات V11-V13.
          </p>
          <code>GET /api/public-web/manifest</code>
        </div>
      </section>
    </main>
  );
}

function LinkLogo({ locale }: { locale: SupportedLocale }) {
  return (
    <a className="kbr-logo" href={localizedPath(locale, '/')}> 
      <span>K</span>
      <strong>KABEERI</strong>
    </a>
  );
}