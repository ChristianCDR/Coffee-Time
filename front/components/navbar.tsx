import { useState } from 'react';
import Link from 'next/link'

export default function Navbar() {
    const [open, setOpen] = useState(false);

    const toggleMenu = () => {
        
        if(!open) {
            document.body.style.overflow = "hidden";
        }
        else {
            document.body.style.overflow = "auto";
        }
        
        setOpen(!open);
    };

    const returnMenu = (classList : string) => {
        return (
            <div className={classList}>
                <Link href="/" className="hover:text-gray-300 p-2 lg:border-b-1">Menu</Link>
                <Link href="/dashboard" target="blank" className="hover:text-gray-300 p-2 lg:border-b-1">Dashboard</Link>  
                <Link href="#" className="hover:text-gray-300 p-2 lg:px-5 border-white border-1 rounded-xl">S&apos;inscrire</Link>
                <Link href="#" className="hover:text-gray-300 p-2 lg:px-5 border-white border-1 rounded-xl">Se connecter</Link>
            </div>
        )
    }

    return (
        <div className="flex justify-between items-center py-4 px-8 text-white "
            style={{ backgroundColor: "var(--dark-coffee)" }} 
        >
            <div className="flex flex-col items-center lg:text-3xl font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M6 18a6.06 6.06 0 0 0 5.17-6a7.62 7.62 0 0 1 6.52-7.51l2.59-.37c-.07-.08-.13-.16-.21-.24c-3.26-3.26-9.52-2.28-14 2.18C2.28 9.9 1 15 2.76 18.46z"/><path fill="currentColor" d="M12.73 12a7.63 7.63 0 0 1-6.51 7.52l-2.46.35l.15.17c3.26 3.26 9.52 2.29 14-2.17C21.68 14.11 23 9 21.25 5.59l-3.34.48A6.05 6.05 0 0 0 12.73 12"/></svg>
                Coffee Time
            </div>

            <div className="lg:hidden" onClick={() => toggleMenu()}>
                { !open ? <svg xmlns="http://www.w3.org/2000/svg" width={32} height={32} viewBox="0 0 24 24"><path fill="currentColor" d="M4 6a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H5a1 1 0 0 1-1-1m0 6a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H5a1 1 0 0 1-1-1m1 5a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2z"></path></svg> : <svg xmlns="http://www.w3.org/2000/svg" width={26} height={26} viewBox="0 0 15 15"><path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27"/></svg>
                }
            </div>

            <div className="hidden lg:block w-1/2">
                    {returnMenu("flex flex-row justify-around items-left p-4 text-sm font-bold")}
            </div>
            
            <div className={`fixed z-1 right-0 top-23 h-full w-full bg-white transform transition-all duration-800 ease-in-out ${ open ? 'translate-x-0' : 'translate-x-full' }`}>

                {returnMenu("flex flex-col justify-around h-1/3 text-sm text-black font-bold")}

            </div>
        </div>
    );
}