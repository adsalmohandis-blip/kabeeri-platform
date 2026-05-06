import type { ReactNode } from 'react';

export type SurfaceCardProps = {
  eyebrow?: string;
  title: string;
  children: ReactNode;
  accent?: 'bronze' | 'olive' | 'sky' | 'clay';
};

export function SurfaceCard({ eyebrow, title, children, accent = 'bronze' }: SurfaceCardProps) {
  return (
    <article className="kbr-card" data-accent={accent}>
      {eyebrow ? <p className="kbr-card__eyebrow">{eyebrow}</p> : null}
      <h3>{title}</h3>
      <div className="kbr-card__body">{children}</div>
    </article>
  );
}