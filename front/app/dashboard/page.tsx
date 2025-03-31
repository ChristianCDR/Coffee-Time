"use client";

import Navbar from "@/components/navbar";
import CoffeeProgress from "@/components/coffeeProgress";

export default function Dashboard() {    

    // useEffect(() => {
    //     const printData = () => {
    //         const urlParams = new URLSearchParams(window.location.search);
    //         const param = urlParams.get('param');
    //         console.log(param);
    //     }
    //     printData();
    // });

    const stopProcess = async () => {
        const response = await fetch("http://localhost:8001/stop-process", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
        });
        if (response.ok) {
            console.log("Processus arrêté");
        }
        else {
            console.error("Erreur lors de l'arrêt du processus");
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
            <button className="border-2 p-2" onClick={stopProcess}>Arrêter le processus</button>
        </div>
    );
}