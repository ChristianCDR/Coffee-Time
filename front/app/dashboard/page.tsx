"use client";

import { useState } from "react";
import CoffeeProgress from "@/components/coffeeProgress";
import Queues from "@/components/queues";
import History from "@/components/history";
import dynamic from 'next/dynamic'

const NavbarNoSSR = dynamic(() => import("@/components/navbar"), { ssr: false })

export default function Dashboard() {    
    const [message, setMessage] = useState("");

    const manageProcess = async (action: string) => {
        const response = await fetch(`http://localhost:8001/api/${action}-process`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        });
        if (response.ok) {
            setMessage("Succès!");
        }
        else {
            setMessage("Une erreur est survenue.");
        }
    }

    return (
        <div>
            <NavbarNoSSR />
            <h1 className="font-bold text-3xl text-center py-3">Dashboard</h1>
            
            <div className="flex flex-row justify-around items-center py-3 w-1/2 mx-auto">
                <button className="border-2 rounded-xl p-2 bg-green-500 font-bold active:bg-white" onClick={() => manageProcess('start')}>Démarrer le processus</button>
                <button className="border-2 border-black rounded-xl p-2 bg-red-500 font-bold text-white active:bg-white" onClick={() => manageProcess('stop')}>Arrêter le processus</button>
                <button className="border-2 rounded-xl p-2 bg-blue-300 font-bold active:bg-white" onClick={() => manageProcess('restart')}>Redémarrer le processus</button>
            </div>

            {message && <p className="text-center font-bold">{message}</p>}
            
            <div className="flex flex-col items-center my-5">
                <div className="flex flex-row justify-around items-center py-3 w-1/2 mx-auto">
                    <CoffeeProgress />
                    <Queues />
                </div>
                <div className="w-2/3 mt-10">
                    <History />
                </div>
            </div>
        </div>
    );
}