"use client";

import { useState } from "react";
import Navbar from "@/components/navbar";
import Image from "next/image";

export default function order () {
    const [type, setType] = useState("coffee");
    const [size, setSize] = useState("small");
    const [intensity, setIntensity] = useState("medium");

    const coffeeImages = new Map();
    coffeeImages.set("espresso", "/coffees/espresso.jpg");
    coffeeImages.set("leppuccino", "/coffees/leppuccino.webp");
    coffeeImages.set("cappuccino", "/coffees/cappuccino.webp");
    coffeeImages.set("mocha", "/coffees/mocha.webp");
    coffeeImages.set("espressino", "/coffees/espressino.jpg");
    coffeeImages.set("cafeViennois", "/coffees/ee.jpg");
    coffeeImages.set("cafeLait", "/coffees/cafeLait.jpg");
    coffeeImages.set("macchiato", "/coffees/macchiato.webp");

    const coffeeImagesArray = Array.from(coffeeImages);
    
    const coffeeSizes = ["small", "medium", "large"];
    const coffeeIntensities = ["light", "medium", "strong"];

    return (
        <div>
            <Navbar />
            <h1 className="font-bold text-3xl text-center py-3">Personnalisez votre café</h1>
            <div>
                <h2 className="text-xl text-center py-3">Choisissez votre type, votre taille et votre intensité</h2>
                <div className="flex flex-wrap justify-around max-w-5/6 mx-auto">
                    { coffeeImagesArray.map(([key, value]) => (
                        <div className="flex flex-col justify-around rounded-xl p-2 m-2 h-100"
                            key={key}
                        >
                            <Image
                                className="rounded-xl"
                                style={{ boxShadow:'5px 5px 20px var(--dark-coffee)'}}
                                src={value}
                                alt={key}
                                width={300}
                                height={100}
                            />
                            <p className="font-bold text-2xl capitalize"
                                style={{ color: "var(--dark-coffee)" }}
                            >{key}</p>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

