import { c as React, l as e, o as useStore, p as cn } from "./index-Dmn91ErK.js";

function FeatherIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("path", { d: "M12.67 19a2 2 0 0 0 1.416-.588l6.154-6.172a6 6 0 0 0-8.49-8.49L5.586 9.914A2 2 0 0 0 5 11.328V18a1 1 0 0 0 1 1z" }),
      e.jsx("path", { d: "M16 8 2 22" }),
      e.jsx("path", { d: "M17.5 15H9" })
    ]
  });
}

function BookOpenIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("path", { d: "M12 7v14" }),
      e.jsx("path", { d: "M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" })
    ]
  });
}

function CheckCircleIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("path", { d: "M22 11.08V12a10 10 0 1 1-5.93-9.14" }),
      e.jsx("path", { d: "m9 11 3 3L22 4" })
    ]
  });
}

function SparklesIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("path", { d: "M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z" }),
      e.jsx("path", { d: "M20 3v4" }),
      e.jsx("path", { d: "M22 5h-4" })
    ]
  });
}

function AwardIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("circle", { cx: "12", cy: "8", r: "6" }),
      e.jsx("path", { d: "m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526" })
    ]
  });
}

function GlobeIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("circle", { cx: "12", cy: "12", r: "10" }),
      e.jsx("path", { d: "M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" }),
      e.jsx("path", { d: "M2 12h20" })
    ]
  });
}

function ArrowLeftIcon({ className }) {
  return e.jsx("svg", {
    xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: "2.5", strokeLinecap: "round", strokeLinejoin: "round",
    className,
    children: [
      e.jsx("path", { d: "M19 12H5" }),
      e.jsx("path", { d: "m12 19-7-7 7-7" })
    ]
  });
}

const STEPS_AR = [
  { num: "01", title: "تقديم المخطوطة", desc: "إرسال العمل كاملاً مع ملخص ونبذة عن الكاتب عبر نموذج التقديم." },
  { num: "02", title: "التقييم الأدبي", desc: "مراجعة العمل وتقييمه من قِبل لجنة القراءة المتخصصة بالدار." },
  { num: "03", title: "التعاقد المعتمد", desc: "توقيع عقد نشر رسمي يضمن كامل حقوق الملكية الفكرية والتوزيع." },
  { num: "04", title: "الإنتاج والتوزيع", desc: "تدقيق لغوي، إخراج فني متميز، طباعة فاخرة وتوزيع محلي ودولي." }
];

const STEPS_EN = [
  { num: "01", title: "Submit Manuscript", desc: "Send the complete work along with a synopsis via our submission form." },
  { num: "02", title: "Editorial Review", desc: "Literary appraisal and evaluation by our reading committee." },
  { num: "03", title: "Publishing Agreement", desc: "Formal contract safeguarding intellectual property & royalties." },
  { num: "04", title: "Release & Distribution", desc: "Copyediting, typesetting, luxury printing and global distribution." }
];

const ADVANTAGES_AR = [
  { icon: CheckCircleIcon, title: "تدقيق ومراجعة لغوية", desc: "مراجعة لغوية متخصصة للنص." },
  { icon: SparklesIcon, title: "تصميم وإخراج احترافي", desc: "أغلفة مبتكرة وتنسيق داخلي فاخر." },
  { icon: AwardIcon, title: "ترقيم دولي وحماية الحقوق", desc: "استخراج رقم الإيداع والـ ISBN رسمياً." },
  { icon: GlobeIcon, title: "توزيع محلي ودولي", desc: "حضور في كبرى معارض الكتاب وشحن للعالم." }
];

const ADVANTAGES_EN = [
  { icon: CheckCircleIcon, title: "Expert Copyediting", desc: "Comprehensive linguistic & proofreading review." },
  { icon: SparklesIcon, title: "Custom Cover & Layout", desc: "Bespoke cover art & elegant typography." },
  { icon: AwardIcon, title: "ISBN & Legal Rights", desc: "Official deposit registration & copyright protection." },
  { icon: GlobeIcon, title: "Global Book Distribution", desc: "Book fair presence & worldwide shipping." }
];

function PublishPage() {
  const isAr = useStore((s) => s.locale) === "ar";
  const steps = isAr ? STEPS_AR : STEPS_EN;
  const advantages = isAr ? ADVANTAGES_AR : ADVANTAGES_EN;

  return e.jsxs("div", {
    className: "min-h-screen bg-background pb-20",
    children: [
      // Hero Header
      e.jsx("section", {
        className: "relative bg-gradient-to-b from-primary/10 via-background to-background border-b border-border/50 py-12 md:py-16",
        children: e.jsxs("div", {
          className: "container-page text-center max-w-2xl mx-auto space-y-4 px-4",
          children: [
            e.jsxs("div", {
              className: "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold",
              children: [
                e.jsx(FeatherIcon, { className: "h-4 w-4" }),
                e.jsx("span", { children: isAr ? "دار نشر مدينة الأدباء" : "Madinat Al-Odabaa Publishing" })
              ]
            }),
            e.jsx("h1", {
              className: "font-display font-black text-3xl md:text-5xl text-foreground tracking-tight",
              children: isAr ? "انشر كتابك معنا" : "Publish With Us"
            }),
            e.jsx("p", {
              className: "text-muted-foreground text-sm md:text-base leading-relaxed",
              children: isAr
                ? "نفتح أبوابنا للمؤلفين والمبدعين لنحول أعمالهم إلى كتب مطبوعة بمعايير عالمية تصل إلى القراء حول العالم."
                : "We partner with visionary authors to produce world-class publications and reach readers worldwide."
            }),
            // Direct Primary Action
            e.jsx("div", {
              className: "pt-3",
              children: e.jsxs("a", {
                href: "/submit-work",
                className: "inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-white font-bold text-base md:text-lg shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all bg-primary hover:bg-primary-hover",
                children: [
                  e.jsx("span", { children: isAr ? "قدّم عملك للنشر الآن" : "Submit Your Work Now" }),
                  e.jsx(ArrowLeftIcon, { className: isAr ? "h-5 w-5" : "h-5 w-5 rotate-180" })
                ]
              })
            })
          ]
        })
      }),

      // Main Content
      e.jsxs("div", {
        className: "container-page max-w-4xl mx-auto px-4 py-12 space-y-24 md:space-y-32",
        children: [
          // 4-Step Publishing Workflow
          e.jsxs("section", {
            className: "space-y-6",
            children: [
              e.jsxs("div", {
                className: "text-center space-y-1",
                children: [
                  e.jsx("h2", {
                    className: "font-display font-black text-2xl md:text-3xl text-foreground",
                    children: isAr ? "رحلة نشر كتابك في 4 خطوات" : "Your Publishing Journey in 4 Steps"
                  }),
                  e.jsx("p", {
                    className: "text-xs md:text-sm text-muted-foreground",
                    children: isAr ? "خطوات واضحة وسريعة من المخطوطة حتى معارض الكتاب" : "A transparent and streamlined process from draft to readers"
                  })
                ]
              }),
              e.jsx("div", {
                className: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4",
                children: steps.map((s, idx) =>
                  e.jsxs("div", {
                    key: idx,
                    className: "relative p-5 rounded-2xl border border-border bg-card shadow-sm hover:border-primary/40 transition-colors flex flex-col justify-between space-y-3",
                    children: [
                      e.jsxs("div", {
                        className: "flex items-center justify-between",
                        children: [
                          e.jsx("span", {
                            className: "font-display font-black text-2xl text-primary/30",
                            children: s.num
                          }),
                          e.jsx("div", { className: "h-2 w-2 rounded-full bg-primary/20" })
                        ]
                      }),
                      e.jsx("h3", {
                        className: "font-display font-bold text-base text-foreground",
                        children: s.title
                      }),
                      e.jsx("p", {
                        className: "text-xs text-muted-foreground leading-relaxed",
                        children: s.desc
                      })
                    ]
                  })
                )
              })
            ]
          }),

          // Key Advantages
          e.jsxs("section", {
            style: { marginTop: "100px", paddingTop: "60px", borderTop: "1px solid rgba(0,0,0,0.08)" },
            className: "space-y-8",
            children: [
              e.jsxs("div", {
                className: "text-center space-y-1",
                children: [
                  e.jsx("h2", {
                    className: "font-display font-black text-2xl md:text-3xl text-foreground",
                    children: isAr ? "مميزات النشر مع مدينة الأدباء" : "Why Publish With Us"
                  }),
                  e.jsx("p", {
                    className: "text-xs md:text-sm text-muted-foreground",
                    children: isAr ? "نضمن لك تجربة نشر احترافية تليق بجهدك وإبداعك" : "We guarantee a prestigious and professional publishing experience"
                  })
                ]
              }),
              e.jsx("div", {
                className: "grid grid-cols-1 sm:grid-cols-2 gap-4",
                children: advantages.map((adv, idx) =>
                  e.jsxs("div", {
                    key: idx,
                    className: "flex items-start gap-3.5 p-4 rounded-xl border border-border/80 bg-card hover:bg-muted/30 transition-colors",
                    children: [
                      e.jsx("div", {
                        className: "grid h-10 w-10 place-items-center rounded-lg bg-primary/10 text-primary shrink-0",
                        children: e.jsx(adv.icon, { className: "h-5 w-5" })
                      }),
                      e.jsxs("div", {
                        className: "space-y-0.5",
                        children: [
                          e.jsx("h3", { className: "font-display font-bold text-sm text-foreground", children: adv.title }),
                          e.jsx("p", { className: "text-xs text-muted-foreground leading-relaxed", children: adv.desc })
                        ]
                      })
                    ]
                  })
                )
              })
            ]
          }),

          // Final Call to Action
          e.jsx("section", {
            style: { marginTop: "100px" },
            className: "relative rounded-3xl overflow-hidden p-10 md:p-14 text-center text-white shadow-2xl",
            style: {
              background: "linear-gradient(135deg, #091D34 0%, #0F3760 40%, #155088 100%)"
            },
            children: e.jsxs("div", {
              className: "relative z-10 max-w-2xl mx-auto space-y-6",
              children: [
                e.jsxs("div", {
                  className: "inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold",
                  style: {
                    background: "linear-gradient(135deg, #FDE68A 0%, #F59E0B 100%)",
                    color: "#0A2540"
                  },
                  children: [
                    e.jsx(SparklesIcon, { className: "h-3.5 w-3.5" }),
                    e.jsx("span", { children: isAr ? "خطوتك الأولى نحو عالم النشر" : "Your First Step Into Publishing" })
                  ]
                }),
                e.jsx("h3", {
                  className: "font-display font-black text-2xl md:text-4xl text-white tracking-tight leading-snug",
                  children: isAr ? "هل مخطوطتك جاهزة للانطلاق؟" : "Is Your Manuscript Ready to Launch?"
                }),
                e.jsx("p", {
                  className: "text-sm md:text-base text-white/80 leading-relaxed max-w-lg mx-auto",
                  children: isAr
                    ? "أرسل عملك الآن واجعل دار مدينة الأدباء بوابتك نحو عالم النشر الورقي والانتشار في معارض الكتاب الدولية."
                    : "Submit your work now and make Madinat Al-Odabaa your gateway to global book distribution."
                }),
                e.jsx("div", {
                  className: "pt-4",
                  children: e.jsxs("a", {
                    href: "/submit-work",
                    className: "inline-flex items-center justify-center gap-3 px-10 md:px-14 py-5 md:py-6 rounded-2xl font-black text-lg md:text-xl shadow-2xl hover:scale-105 active:scale-95 transition-all group border-2 border-amber-300/40",
                    style: {
                      background: "linear-gradient(135deg, #FDE68A 0%, #F59E0B 100%)",
                      color: "#0A2540",
                      boxShadow: "0 15px 35px -5px rgba(245, 158, 11, 0.4)"
                    },
                    children: [
                      e.jsx("span", {
                        className: "group-hover:tracking-wider transition-all",
                        children: isAr ? "ابدأ تقديم عملك الآن" : "Start Your Submission Now"
                      }),
                      e.jsx(ArrowLeftIcon, { className: isAr ? "h-6 w-6 stroke-[3] group-hover:-translate-x-1 transition-transform" : "h-6 w-6 stroke-[3] rotate-180 group-hover:translate-x-1 transition-transform" })
                    ]
                  })
                })
              ]
            })
          })
        ]
      })
    ]
  });
}

export { PublishPage as component };
