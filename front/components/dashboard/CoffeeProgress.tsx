'use client';

import { useCoffeeProgress } from "@/hooks/useCoffeeProgress";
import { CoffeeProgressData } from "@/types/index";

export default function CoffeeProgress() {

    const { data, error } = useCoffeeProgress<CoffeeProgressData>();

    if (error) return <p className="text-red-500">{error}</p>;

    return (
        <div>
            <h1 className="font-bold">État du Processus</h1>
            { data ? (
                <div>
                    <p>Commande: {data.orderId}</p>
                    <p>Progression: {data.progress}%</p>
                </div>
            ) : (
                <p>En attente de mise à jour...</p>
            )}
        </div>
    );
}
