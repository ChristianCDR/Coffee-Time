"use client";

import { useState } from "react";
import Navbar from "@/components/navbar";
import Image from "next/image";

export default function order () {
    const [name, setName] = useState("");
    const [size, setSize] = useState("");
    const [intensity, setIntensity] = useState("");

    const coffeeImages = new Map();
    coffeeImages.set("espresso", "/coffees/espresso.jpg");
    coffeeImages.set("leppuccino", "/coffees/leppuccino.webp");
    coffeeImages.set("cappuccino", "/coffees/cappuccino.webp");
    coffeeImages.set("mocha", "/coffees/mocha.webp");
    coffeeImages.set("espressino", "/coffees/espressino.jpg");
    coffeeImages.set("cafeViennois", "/coffees/ee.jpg");
    coffeeImages.set("cafeLait", "/coffees/cafeLait.jpg");
    coffeeImages.set("macchiato", "/coffees/macchiato.webp");

    const sizeIcon = (size: number) => {
        return <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24"><path fill="currentColor" d="M7 22h10a1 1 0 0 0 .99-.858L19.867 8H21V6h-1.382l-1.724-3.447A1 1 0 0 0 17 2H7c-.379 0-.725.214-.895.553L4.382 6H3v2h1.133L6.01 21.142A1 1 0 0 0 7 22m10.418-11H6.582l-.429-3h11.693zm-9.551 9l-.429-3h9.123l-.429 3zM7.618 4h8.764l1 2H6.618z"/></svg>
    }

    const coffeeSizes = new Map();
    coffeeSizes.set("small", sizeIcon(24));
    coffeeSizes.set("medium", sizeIcon(32));
    coffeeSizes.set("large", sizeIcon(40));
    
    const coffeeIntensities = new Map();
    coffeeIntensities.set("light", "var(--light-coffee)");
    coffeeIntensities.set("medium", "var(--medium-coffee)");
    coffeeIntensities.set("strong", "var(--dark-coffee)");

    const coffeeImagesArray = Array.from(coffeeImages);
    const coffeeSizesArray = Array.from(coffeeSizes);
    const coffeeIntensitiesArray = Array.from(coffeeIntensities);

    return (
        <div>
            <Navbar />
            <h1 className="font-bold text-3xl text-center py-3">Personnalisez votre café</h1>
            <div>
                <h2 className="text-xl text-center py-3">Choisissez votre type, votre taille et votre intensité</h2>
                <div className="flex flex-wrap justify-around max-w-5/6 mx-auto">
                    { coffeeImagesArray.map(([key, value]) => (
                        <div className="flex flex-col justify-around rounded-xl p-2 m-2 h-150"
                            key={key}
                        >
                            <Image
                                className="rounded-xl"
                                style={{ boxShadow: '5px 5px 20px var(--dark-coffee)'}}
                                src={value}
                                alt={key}
                                width={300}
                                height={100}
                            />
                            <p className="font-bold text-2xl capitalize"
                                style={{ color: "var(--dark-coffee)" }}
                            >{key}</p>
                            <div className="flex flex-row justify-between">
                                <div className="flex flex-col justify-between w-2/5">
                                    <span className="font-bold text-lg">Intensité</span>
                                    <div className="flex flex-row justify-between items-end">
                                        { coffeeIntensitiesArray.map(([intensityKey, intensityValue]) => (
                                            <button className="rounded-xl w-7 h-7 p-1 rounded-full hover:border-3 hover:border-white"
                                                key={intensityKey}
                                                style={{
                                                    backgroundColor: intensityValue,
                                                    borderColor: intensity === intensityKey && name === key ? "white" : "transparent",
                                                    borderWidth: intensity === intensityKey && name === key ? 2 : 0
                                                }}
                                                onClick={() => { setIntensity(intensityKey); setName(key) }}
                                            > 
                                            </button>
                                        ))}
                                    </div>
                                </div>
                                <div className="">
                                    <span className="font-bold text-lg">Taille</span>
                                    <div className="flex flex-row justify-between items-end">
                                        { coffeeSizesArray.map(([sizeKey, sizeValue]) => (
                                            <button className="p-1 mx-1 rounded-xl"
                                                key={sizeKey}
                                                style={{ backgroundColor: size === sizeKey && name === key ? "var(--light-coffee)" : "" }}
                                                onClick={() => { setSize(sizeKey); setName(key) }}
                                            >
                                                {sizeValue}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                            <button className="font-bold text-lg border-1 rounded-xl w-1/2 h-10 mx-auto">Commander</button>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

