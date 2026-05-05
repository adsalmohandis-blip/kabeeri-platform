# Release Notes — v1.2.1

## هدف الإصدار

تحديث الحزمة بعد اعتماد أن Kabeeri Mall يحتوي على كتالوجات عامة متعددة للمنتجات والخدمات والبزنسز/المشاريع، تعمل عبر كل التطبيقات المستضافة داخل Kabeeri أو المتصلة خارجيًا مثل WordPress/WooCommerce.

## الملفات المضافة

- 00_SYSTEM_INDEX/01_System_Features_Catalog_AR.docx
- 14_MALL_MARKETPLACE_NETWORK/01_Mall_Multi_Catalog_Network_Architecture_AR.docx
- 04_DATABASE_ARCHITECTURE/04_Master_Database_Maps/06_Database_v1.2.1_Mall_Multi_Catalog_Addendum_AR.docx

## قرارات معمارية

- Product Mall يعرض المنتجات العامة من كل التطبيقات والمصادر بعد consent.
- Service Mall يعرض الخدمات العامة القابلة للطلب أو الحجز.
- Business & Projects Mall يعرض البزنسز والتطبيقات/المشاريع العامة.
- WordPress/WooCommerce يمكن أن يكون مصدرًا خارجيًا عبر connector أو feed، بدون قراءة مباشرة للبيانات الخاصة.
- كل ظهور في المول يعتمد على Mall Mirror + Publication Consent + Source Traceability.

## الحالة

الإصدار v1.2.1 يحافظ على جاهزية coding-ready، لكنه يضيف Addendum واضحًا يجب مراعاته عند تنفيذ Mall/Marketplace migrations.
