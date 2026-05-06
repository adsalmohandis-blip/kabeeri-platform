import Link from 'next/link';
import type { ReactNode } from 'react';

export type ActionLinkProps = {
  href: string;
  children: ReactNode;
  tone?: 'primary' | 'ghost';
};

export function ActionLink({ href, children, tone = 'primary' }: ActionLinkProps) {
  return (
    <Link className={`kbr-action kbr-action--${tone}`} href={href}>
      {children}
    </Link>
  );
}