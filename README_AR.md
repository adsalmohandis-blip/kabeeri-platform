# إطار Kabeeri Vibe Developer Framework

Kabeeri VDF، أو `kabeeri.vdf`، هو إطار عمل مفتوح المصدر من نوع meta-framework يساعد على بناء البرمجيات بمساندة أدوات الذكاء الاصطناعي.

هو لا يستبدل Laravel أو Next.js أو React أو Vue أو Angular أو WordPress أو Django أو .NET أو Flutter أو React Native.  
بدلًا من ذلك، يوفّر طبقة حوكمة وتنظيم للعمل قبل التوليد البرمجي وأثناءه وبعده.

Kabeeri يساعد على تحويل فكرة المنتج إلى:

- أسئلة واضحة للمطور أو العميل
- حدود واضحة للتطبيقات
- خطط تسليم Agile أو Structured
- خرائط المنتج
- توجيه لتصميم قاعدة البيانات
- توجيه لتجربة المستخدم والواجهات
- مهام محكومة وقابلة للتتبع
- سياق جاهز لأوامر الذكاء الاصطناعي
- سجلات التوكنات والتكلفة
- لوحة مراقبة مباشرة
- بوابات جاهزية وإطلاق
- سجلات Evolution Steward الخاصة بتحديثات Kabeeri نفسه
- تقارير handoff

الفكرة الأساسية بسيطة: يتحدث المطور بشكل طبيعي، ويستخدم مساعد الذكاء الاصطناعي Kabeeri كطبقة تشغيل للمشروع، ويبقى المشروع مفهومًا حتى بعد توقف الجلسات واستئنافها.

## أولًا الفكرة العملية

Kabeeri مبني على أسلوب `vibe coding` أولًا.

إذا كنت تعمل بأسلوب vibe coding، فلا تبدأ بحفظ أوامر CLI.  
ابدأ بوصف المنتج لمساعد الذكاء الاصطناعي واطلب منه استخدام Kabeeri لتنظيم العمل.

التجربة الطبيعية ليست حفظ أوامر طرفية كثيرة، بل تكون هكذا:

```text
المطور يتحدث إلى مساعد الذكاء الاصطناعي بلغة طبيعية.
المساعد يستخدم قواعد Kabeeri وسجلاته في الخلفية.
Kabeeri ينظم المهام والقرارات والحدود والاستهلاك والالتقاطات وحالة لوحة المراقبة.
```

مثال:

```text
أريد بناء متجر إلكتروني فيه Laravel backend وNext.js storefront،
ولوحة إدارة، ودفع، وشحن، وتطبيق موبايل لاحقًا.
```

بعدها يجب أن يستخدم المساعد Kabeeri لتحديد blueprint للمنتج، وطرح الأسئلة الناقصة، واقتراح نمط التسليم المناسب، وتقسيم العمل إلى مهام، وتتبع استهلاك التوكنات، وتحديث لوحة المتابعة.

## ما هو دور `kvdf`؟

Kabeeri يحتوي على CLI فعلي باسم `kvdf`، لكن `kvdf` هو المحرك وليس تجربة المنتج الأساسية.

الـ CLI موجود كي يتمكن مساعد الذكاء الاصطناعي والأتمتة والمطور المتقدم من تحديث حالة المشروع بشكل موثوق.  
يمكنه إنشاء `.kabeeri/`، وتوليد هياكل المشروع، وإنشاء مهام محكومة، وتشغيل التحقق، وتسجيل استهلاك الذكاء الاصطناعي، وإدارة task access tokens، وتشغيل لوحة المتابعة، وتطبيق policy gates، وإخراج تقارير الجاهزية والحوكمة.

مصدر الحقيقة هو:

- `.kabeeri/` مجلد حالة التشغيل المحلي للمشروع
- الـ CLI يكتب سجلات `.kabeeri/`
- لوحة المتابعة تقرأ من `.kabeeri/`
- أدوات الذكاء الاصطناعي يجب أن تعمل عبر المهام والحدود والالتقاطات والتقارير بدلًا من التعديل العشوائي

لأغلب المطورين، الفكرة المهمة هي: تحدث مع المساعد طبيعيًا، ودعه يستخدم `kvdf` فقط عندما يحتاج المشروع إلى تتبع، أو تحديث لوحة، أو تسجيل مهام، أو توثيق استخدام، أو فحص حوكمة.

لرؤية الخريطة الكاملة للقدرات، راجع:

- [System Capabilities Reference](docs/SYSTEM_CAPABILITIES_REFERENCE.md)
- [CLI Command Reference](cli/CLI_COMMAND_REFERENCE.md)
- [Documentation Site](docs/site/index.html)

## لمن هذا المشروع؟

Kabeeri مفيد لـ:

- مطوري vibe coding
- المؤسسين الذين يبنون بالذكاء الاصطناعي
- المطورين الذين يريدون تتبع العمل الناتج عن الذكاء الاصطناعي
- الفرق الصغيرة التي تستخدم مساعدين ذكيين
- الوكالات التي تسلم مشاريع متكررة
- أصحاب المنتجات الذين يحتاجون تخطيطًا أوضح قبل التنفيذ
- المطورين الذين يعملون على أكثر من تطبيق داخل نفس المنتج

يمكنك استخدامه مع أي مساعد ذكاء اصطناعي، وليس مرتبطًا بأداة واحدة.

## لماذا نحتاجه؟

أدوات الذكاء الاصطناعي قوية، لكن المشاريع تفشل عندما:

- تكون الفكرة غير واضحة
- يبدأ الذكاء الاصطناعي في الكود قبل وجود مهام
- تختلط ملفات الـ backend والـ frontend وmobile
- يعمل أكثر من مطور على نفس الملفات
- لا يتم تسجيل استهلاك التوكنات
- تتأخر لوحة المتابعة أو تصبح قديمة
- يتم العمل خارج نطاق المهمة المتفق عليها
- لا يعرف المالك ما هو الجاهز أو المتوقف أو عالي المخاطر

Kabeeri يوفّر طبقة الحوكمة حول تجربة التطوير بالذكاء الاصطناعي.

## الإعداد السريع للمحرك

هذا الإعداد مخصص للمطور المتقدم أو الأتمتة أو مساعد الذكاء الاصطناعي الذي يحتاج المحرك محليًا.

تثبيت الاعتمادات:

```bash
npm install
```

فحص المحرك:

```bash
npm run kvdf -- --help
npm run kvdf -- doctor
npm run kvdf -- validate
```

بعد الربط المحلي أو التثبيت، يمكن استخدام `kvdf` مباشرة.

## ابدأ مع مساعد الذكاء الاصطناعي

افتح المستودع أو مجلد المشروع في محرر الشيفرة، ثم قل لمساعد الذكاء الاصطناعي شيئًا مثل:

```text
استخدم Kabeeri لمساعدتي في بدء مشروع متجر إلكتروني جديد.
اسألني فقط عن الأسئلة الناقصة، واقترح نمط التسليم، وأنشئ مهامًا محكومة،
وحافظ على تحديث لوحة المتابعة. لا تنفذ خارج المهام المعتمدة.
```

بعدها يمكن للمساعد استخدام سجلات Kabeeri ومحرك CLI في الخلفية.

## ماذا تطلب من الذكاء الاصطناعي أولًا؟

لمنتج جديد:

```text
استخدم Kabeeri كنظام تشغيل لهذا المشروع.
ابدأ بفهم المنتج، واسألني فقط عن الأسئلة الناقصة،
واختر نمط التسليم المناسب بموافقتي، وأنشئ مهامًا محكومة،
وتابع استهلاك الذكاء الاصطناعي، وحافظ على تحديث لوحة المتابعة.
```

ولمشروع موجود:

```text
استخدم Kabeeri لتحليل قاعدة الكود الحالية.
لا تعِد كتابة المشروع. حدّد حدود التطبيق، والتقنيات، والمخاطر،
والمهام الناقصة، وحالة لوحة المتابعة، وما الذي يجب فعله بعد ذلك.
```

## حالة مساحة العمل

داخل المجلد الذي تريد أن يكون له state محلي:

```bash
kvdf init --profile standard --mode structured
```

إذا كان الأمر تفاعليًا، سيسأل Kabeeri سؤالًا قصيرًا عن التطبيق الذي تريد بناءه، ثم ينشئ أسئلة intake التكيفية ومهام docs-first مباشرة.

للأتمتة أو عندما تطلب من المساعد تنفيذ ذلك مباشرة:

```bash
kvdf init --profile standard --goal "Build ecommerce store with Laravel backend and Next.js frontend"
```

ولإنشاء الحالة فقط بدون intake الأول:

```bash
kvdf init --no-intake
```

افتراضيًا، يحفظ Kabeeri `language: user`، أي أن الأسئلة والتوجيهات تتبع لغة المستخدم المكتشفة ما لم تحدد ذلك يدويًا:

```bash
kvdf init --profile standard --lang en
kvdf init --profile standard --lang ar
```

## ملفات المشروع

| Profile | مناسب لـ |
| --- | --- |
| `lite` | صفحات هبوط، MVP صغير، أدوات بسيطة |
| `standard` | SaaS، متجر إلكتروني، CMS، حجز، أنظمة أعمال |
| `enterprise` | ERP، marketplaces، أنظمة متعددة المستأجرين، منصات طويلة العمر |

يمكن للمساعد إنشاء skeleton حسب profile المختار.  
ويمكن للمطور المتقدم استخدام:

```bash
kvdf create --profile standard --output my-project
```

أو:

```bash
kvdf generate --profile standard --output my-project
```

عندما يُشغَّل الأمر داخل workspace مهيأ بـ `.kabeeri`، ينشئ Kabeeri أيضًا مهامًا حوكميّة مقترحة للمراجعة والتنفيذ والتحقق.

وللحصول على skeleton خام فقط:

```bash
kvdf create --profile standard --output my-project --no-tasks
```

## سير العمل المقترح

1. ابدأ تهيئة Kabeeri.
2. أجب عن سؤال هدف التطبيق في جملة واحدة.
3. دع Kabeeri ينشئ أسئلة intake التكيفية.
4. أكمل مهام docs-first: الإجابات، النطاق، المعمارية، التصميم البياني، اتجاه الواجهة، وقائمة التنفيذ.
5. حوّل الوثائق المعتمدة إلى مهام تنفيذية.
6. نفّذ مهمة واحدة في كل مرة.
7. استخدم task tokens وlocks لتحديد نطاق التنفيذ.
8. سجّل استهلاك التوكنات.
9. سجّل أي عمل تم خارج المسار المعتاد.
10. راجع وحقق وسلّم.

هذا gate مقصود، حتى لا ينتقل الذكاء الاصطناعي من فكرة عامة إلى تنفيذ مباشر في Laravel أو Next.js أو React أو WordPress أو mobile قبل اكتمال ومراجعة مهام التوثيق.

## أمثلة خلفية

```bash
kvdf questionnaire plan "Build an ecommerce store with Laravel backend, Next.js frontend, payments, shipping, and a mobile app" --json
kvdf blueprint recommend "Build ecommerce store with catalog cart checkout payments shipping"
kvdf data-design context ecommerce --json
kvdf design recommend ecommerce --json
kvdf design reference-recommend "admin ecommerce dashboard with orders revenue products and notifications"
kvdf design reference-questions ADMIT-ADB02
kvdf delivery recommend "Build a regulated ERP with accounting and approvals" --json
```

## أمثلة للأتمتة

```bash
kvdf vibe suggest "Add a checkout page for customers"
kvdf vibe plan "Build ecommerce store with products cart checkout admin and tests"
kvdf vibe convert suggestion-001
kvdf capture --summary "Implemented checkout validation" --files src/checkout.ts --checks "npm test" --evidence "checkout tests passed"
```

الالتقاطات بعد العمل مهمة. إذا تغيّرت ملفات بدون task مرتبط، قد تتعطل الجاهزية حتى يتم ربط الالتقاط أو تحويله أو رفضه أو حله.

## تتبع المهام والحوكمة

Kabeeri يتوقع أن يتم التنفيذ عبر مهام محكومة.  
ومصدر الحوكمة الموحد هو `knowledge/task_tracking/`.

```bash
kvdf task create --title "Build product catalog API" --workstream backend
kvdf task approve task-001
kvdf task assign task-001 --assignee agent-001
kvdf token issue --task task-001 --assignee agent-001 --max-usage-tokens 50000
kvdf lock acquire --task task-001 --type folder --scope src/api/products --owner agent-001
kvdf task start task-001 --actor agent-001
```

ويمكن عرض حالة المهام بصيغة CLI أو JSON مباشر:

```bash
kvdf task tracker
kvdf task tracker --json
```

## الاستهلاك والتكلفة

```bash
kvdf usage record --task task-001 --developer agent-001 --provider openai --model gpt-4 --input-tokens 1000 --output-tokens 500 --cost 0.25
kvdf usage inquiry --input-tokens 300 --output-tokens 120 --cost 0.04 --operation owner-question
kvdf usage admin --input-tokens 500 --output-tokens 200 --cost 0.08 --operation dashboard-review
kvdf usage summary
kvdf usage efficiency
```

## لوحة المتابعة

```bash
kvdf dashboard serve --port 4177
```

المسارات:

- صفحة المستخدم: `http://127.0.0.1:4177/`
- لوحة المتابعة الخاصة: `http://127.0.0.1:4177/__kvdf/dashboard`
- الحالة المباشرة الكاملة: `http://127.0.0.1:4177/__kvdf/api/state`
- حالة tracker: `http://127.0.0.1:4177/__kvdf/api/tasks`
- التقارير المباشرة: `http://127.0.0.1:4177/__kvdf/api/reports`

## الجاهزية والحوكمة وبوابات الإطلاق

```bash
kvdf readiness report --output readiness.md
kvdf governance report --output governance.md
kvdf readiness report --target release --strict
kvdf governance report --target publish --strict
kvdf reports live
```

## Agile و Structured

```bash
kvdf delivery recommend "Build CRM with pipeline, reporting, and integrations" --json
kvdf delivery choose agile --reason "Client wants iterative delivery"
```

Kabeeri يدعم طريقتين للتسليم:

- Agile: backlog, epics, stories, sprints, reviews, impediments, retrospectives, velocity
- Structured: requirements, phases, deliverables, risks, approvals, gates, traceability

## بناء المشروع

```text
src/                 مصدر CLI
bin/                 نقطة تشغيل kvdf
knowledge/           الحوكمة، المهام، التصميم، Agile، البيانات، وسير العمل
packs/               generators وtemplates وprompt packs وexamples
integrations/        تكاملات dashboard وGitHub وVS Code وmulti-AI
schemas/             schemas الخاصة بالحالة والعقود
docs/                الوثائق والتقارير وsite والـ guides
cli/                 مرجع الأوامر
tests/               اختبارات التكامل
```

حالة المشروع المحلي تُخزّن داخل:

```text
.kabeeri/
```

## التوثيق

ابدأ من هنا:

- [System Capabilities Reference](docs/SYSTEM_CAPABILITIES_REFERENCE.md)
- [CLI Command Reference](cli/CLI_COMMAND_REFERENCE.md)
- [Docs Site](docs/site/index.html)

## التطوير

```bash
npm test
npm run test:smoke
npm run check
```

## الرخصة

Kabeeri Vibe Developer Framework مشروع مفتوح المصدر تحت رخصة MIT.

راجع [LICENSE](LICENSE).
