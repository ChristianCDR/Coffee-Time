'use client';

import Link from 'next/link';
import { Props } from "@/types/index";

export default function MenuLinks ({ className } : Props) {
    return (
        <div className={className}>
            <Link href="/" className="hover:text-gray-300 p-2 lg:border-b-1">Menu</Link>
            <Link href="/dashboard" target="_blank"  rel="noopener" className="hover:text-gray-300 p-2 lg:border-b-1">Dashboard</Link>  
            {/* <Link href="#" className="hover:text-gray-300 p-2 lg:px-5 border-white border-1 rounded-xl">S&apos;inscrire</Link>
            <Link href="#" className="hover:text-gray-300 p-2 lg:px-5 border-white border-1 rounded-xl">Se connecter</Link> */}
        </div>
    )
}