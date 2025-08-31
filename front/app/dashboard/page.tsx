"use client";

import CoffeeProgress from "@/components/dashboard/CoffeeProgress";
import QueueStatus from "@/components/dashboard/QueueStatus";
import History from "@/components/dashboard/History";
import { useManageProcess } from "@/hooks/useManageProcess";
import dynamic from 'next/dynamic';

const NavbarNoSSR = dynamic(() => import("@/components/layout/Navbar"), { ssr: false })

export default function Dashboard() {
    const { message, manageProcess } = useManageProcess();

    return (
        <div>
            <NavbarNoSSR />
            <h1 className="font-bold text-3xl text-center py-3">Dashboard</h1>
            
            <div className="flex flex-col md:flex-row justify-around items-center py-3 w-1/2 mx-auto">
                <button className="border-2 m-5 border-black rounded-xl p-2 bg-red-500 font-bold text-white active:bg-white" onClick={() => manageProcess('stop')}>Arrêter le processus</button>
                <button className="border-2 m-5 rounded-xl p-2 bg-blue-300 font-bold active:bg-white" onClick={() => manageProcess('restart')}>Redémarrer le processus</button>
            </div>

            {message && <p className="text-center font-bold">{message}</p>}
            
            <div className="flex flex-col items-center my-5">
                <div className="flex flex-row justify-around items-center py-3 w-1/2 mx-auto">
                    <CoffeeProgress />
                    <QueueStatus />
                </div>
                <div className="w-4/5 md:w-2/3 mt-10 overflow-x-auto">
                    <History />
                </div>
            </div>
        </div>
    );
}