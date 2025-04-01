"use client";

import { useState } from "react";
import Navbar from "@/components/navbar";
import CoffeeProgress from "@/components/coffeeProgress";
import Queues from "@/components/queues";

export default function Dashboard() {    
    const [message, setMessage] = useState("");

    const manageProcess = async (action: string) => {
        const response = await fetch(`http://localhost:8001/${action}-process`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
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
            <Navbar />
            <h1 className="font-bold text-3xl text-center py-3">Dashboard</h1>
            <div className="flex flex-col items-center">
                <h2 className="text-xl text-center py-3">Suivez la préparation de votre commande en temps réel</h2>
                <CoffeeProgress/>
            </div>
            <div className="flex flex-row justify-around items-center py-3 w-1/2 mx-auto">
                <button className="border-2 rounded-xl p-2 bg-green-500 font-bold" onClick={() => manageProcess('start')}>Démarrer le processus</button>
                <button className="border-2 border-black rounded-xl p-2 bg-red-500 font-bold text-white" onClick={() => manageProcess('stop')}>Arrêter le processus</button>
                <button className="border-2 rounded-xl p-2 bg-blue-300 font-bold" onClick={() => manageProcess('restart')}>Démarrer le processus</button>
            </div>
            {message && <p className="text-center font-bold">{message}</p>}

            <Queues />
        </div>
    );
}