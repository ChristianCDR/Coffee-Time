"use client";

import { useEffect } from "react";
import Navbar from "@/components/navbar";
import CoffeeProgress from "@/components/coffeeProgress";

export default function Dashboard() {    

    useEffect(() => {
        const printData = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const param = urlParams.get('param');
            console.log(param);
        }
        printData();
    });

    return (
        <div>
            <Navbar />
            <h1 className="font-bold text-3xl text-center py-3">Dashboard</h1>
            <div className="flex flex-col items-center">
                <h2 className="text-xl text-center py-3">Suivez la préparation de votre commande en temps réel</h2>
                <CoffeeProgress/>
            </div>
        </div>
    );
}