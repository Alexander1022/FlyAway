import "leaflet/dist/leaflet.css";
import { LatLngTuple } from "leaflet";
import Footer from "../components/Footer";
import {
  MapContainer,
  TileLayer,
  Marker,
  Popup,
  useMap,
  Polyline,
} from "react-leaflet";
import L from "leaflet";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";
import CustomPopup from "../components/CustomProp";
import UserMarker from "../assets/user-pin.png";
import { useEffect, useState } from "react";
import axios from "axios";
import { useSearchParams } from "react-router";

L.Marker.prototype.options.icon = L.icon({
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

const userIcon = L.icon({
  iconUrl: UserMarker,
  shadowUrl: markerShadow,
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

interface LocationData {
  position: LatLngTuple;
  imageUrl: string;
  date: Date;
  author: string;
  contentText: string;
  altText: string;
}

function CenterMap({ position }: { position: LatLngTuple }) {
  const map = useMap();
  useEffect(() => {
    map.setView(position);
  }, [position, map]);
  return null;
}

export default function MyMap() {
  const [searchParams, setSearchParams] = useSearchParams();
  const [userPosition, setUserPosition] = useState<LatLngTuple | null>(null);
  const [locations, setLocations] = useState<LocationData[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [geolocationBlocked, setGeolocationBlocked] = useState(false);
  const [startDate, setStartDate] = useState(
    searchParams.get("startDate") || ""
  );
  const [endDate, setEndDate] = useState(searchParams.get("endDate") || "");
  const [kilometers, setKilometers] = useState(
    searchParams.get("kilometers") || "1"
  );

  useEffect(() => {
    const newStart = searchParams.get("startDate") || "";
    const newEnd = searchParams.get("endDate") || "";
    const newKm = searchParams.get("kilometers") || "";
    setStartDate(newStart);
    setEndDate(newEnd);
    setKilometers(newKm);
  }, [searchParams]);

  const handleDateChange = (type: "start" | "end", value: string) => {
    if (type === "start") {
      if (endDate && value > endDate) setEndDate(value);
      setStartDate(value);
    } else {
      if (startDate && value < startDate) setStartDate(value);
      setEndDate(value);
    }
  };

  const handleApplyFilters = () => {
    const params = new URLSearchParams();
    if (startDate) params.set("startDate", startDate);
    if (endDate) params.set("endDate", endDate);
    if (kilometers) params.set("kilometers", kilometers);
    setSearchParams(params);
  };

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError(null);

      try {
        const apiParams: {
          startDate?: string;
          endDate?: string;
          kilometers?: string;
          lat?: number;
          lng?: number;
        } = {
          startDate: searchParams.get("startDate") || undefined,
          endDate: searchParams.get("endDate") || undefined,
        };

        const kmParam = searchParams.get("kilometers");
        if (kmParam) {
          apiParams.kilometers = kmParam;
        } else {
          apiParams.kilometers = "1";
        }
        if (userPosition) {
          apiParams.lat = userPosition[0];
          apiParams.lng = userPosition[1];
        }

        const response = await axios.get(
          `${import.meta.env.VITE_SERVER_ENDPOINT}/api/locations`,
          {
            headers: {
              "ngrok-skip-browser-warning": "please-ngrok-we-love-you",
              Authorization: `Bearer ${localStorage.getItem("authToken")}`,
            },
            params: apiParams,
          }
        );

        const responseData = response.data;

        if (!responseData.data || !Array.isArray(responseData.data)) {
          throw new Error("Invalid response format: Expected data array");
        }

        const formattedData = responseData.data.map((loc: any) => {
          const specieName = loc.specie?.common_name || "Unknown species";
          const lat = parseFloat(loc.lat);
          const lng = parseFloat(loc.lng);
          const firstImage = loc.image_urls[0].url;

          return {
            position: [lat, lng] as LatLngTuple,
            imageUrl: firstImage,
            date: new Date(loc.created_at),
            author: loc.user?.name || "Unknown",
            contentText: loc?.specie?.scientific_name,
            altText: `Photo of ${specieName}`,
          };
        });

        setLocations(formattedData);
        setError(null);
      } catch (err) {
        if (axios.isAxiosError(err)) {
          const message = err.response?.data?.message || err.message;
          setError(`${message}`);
        } else {
          setError(err instanceof Error ? err.message : "Unknown error");
        }
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [searchParams, userPosition]);

  useEffect(() => {
    if (typeof window !== "undefined" && navigator.geolocation) {
      const watchId = navigator.geolocation.watchPosition(
        (position) => {
          const { latitude, longitude } = position.coords;
          setUserPosition([latitude, longitude]);
        },
        (error) => {
          console.error("Error getting geolocation:", error);
          if (error.code === 1) {
            setGeolocationBlocked(true);
          }
        },
        {
          enableHighAccuracy: true,
          maximumAge: 10000,
        }
      );

      return () => navigator.geolocation.clearWatch(watchId);
    }
  }, []);

  return (
    <div className="min-h-full flex flex-col relative bg-gradient-to-b from-emerald-50 to-blue-50">
      <div className="flex-1 pt-8 md:pt-10 pb-20 flex">
        <div className="w-full max-w-6xl mx-auto p-4">
          <div className="mb-6 flex flex-wrap gap-4 items-end bg-white/70 backdrop-blur-sm border border-white/30 rounded-2xl shadow-lg px-6 py-4">
            <div className="flex flex-col flex-1 min-w-[180px]">
              <label
                htmlFor="start-date"
                className="text-xs text-gray-700 mb-1 font-medium"
              >
                Start date
              </label>
              <input
                id="start-date"
                type="date"
                placeholder="Start date"
                value={startDate}
                onChange={(e) => handleDateChange("start", e.target.value)}
                className="p-2 border border-emerald-100 rounded-md bg-white/80 focus:outline-none focus:border-emerald-300 focus:ring-2 focus:ring-emerald-200 focus:ring-opacity-50"
                aria-label="Start date"
              />
            </div>
            <div className="flex flex-col flex-1 min-w-[180px]">
              <label
                htmlFor="end-date"
                className="text-xs text-gray-700 mb-1 font-medium"
              >
                End date
              </label>
              <input
                id="end-date"
                type="date"
                placeholder="End date"
                value={endDate}
                onChange={(e) => handleDateChange("end", e.target.value)}
                className="p-2 border border-emerald-100 rounded-md bg-white/80 focus:outline-none focus:border-emerald-300 focus:ring-2 focus:ring-emerald-200 focus:ring-opacity-50"
                aria-label="End date"
                min={startDate}
              />
            </div>
            <div className="flex flex-col flex-1 min-w-[180px]">
              <label
                htmlFor="radius"
                className="text-xs text-gray-700 mb-1 font-medium"
              >
                Radius (km)
              </label>
              <input
                id="radius"
                type="number"
                value={kilometers}
                onChange={(e) => setKilometers(e.target.value)}
                placeholder="Radius (km)"
                className="p-2 border border-emerald-100 rounded-md bg-white/80 focus:outline-none focus:border-emerald-300 focus:ring-2 focus:ring-emerald-200 focus:ring-opacity-50"
                min="0"
                disabled={geolocationBlocked || !userPosition}
                aria-label="Search radius in kilometers"
              />
            </div>
            <button
              onClick={handleApplyFilters}
              className="h-10 px-5 mt-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold shadow transition-colors"
            >
              Apply Filters
            </button>
          </div>

          {geolocationBlocked ? (
            <div className="h-[600px] border-4 border-white/40 rounded-3xl overflow-hidden relative z-0 flex items-center justify-center bg-gray-100 shadow-lg">
              <div className="text-center text-xl p-4">
                Please enable geolocation permissions to view the map.
              </div>
            </div>
          ) : (
            <div className="h-[600px] border-4 border-white/40 rounded-3xl overflow-hidden relative z-0 shadow-lg bg-white/70 backdrop-blur-sm">
              {loading && (
                <div className="absolute inset-0 bg-gray-500/50 flex items-center justify-center z-50 rounded-3xl">
                  <div className="text-white text-xl">Loading map data...</div>
                </div>
              )}

              <MapContainer
                center={userPosition || [42.6977, 23.3219]}
                zoom={15}
                scrollWheelZoom={true}
                style={{ height: "100%", width: "100%" }}
              >
                <TileLayer
                  attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                  url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />

                {!error &&
                  (() => {
                    const locGroups: Record<string, LocationData[]> = {};
                    locations.forEach((loc) => {
                      const key = `${loc.position[0].toFixed(
                        5
                      )},${loc.position[1].toFixed(5)}`;
                      if (!locGroups[key]) locGroups[key] = [];
                      locGroups[key].push(loc);
                    });
                    let markerIdx = 0;
                    return Object.entries(locGroups).flatMap(([_, group]) => {
                      if (group.length === 1) {
                        const location = group[0];
                        let offsetPosition = location.position as LatLngTuple;
                        let showDashedLine = false;
                        if (userPosition) {
                          const [lat1, lng1] = userPosition;
                          const [lat2, lng2] = location.position;
                          const R = 6371000;
                          const dLat = ((lat2 - lat1) * Math.PI) / 180;
                          const dLng = ((lng2 - lng1) * Math.PI) / 180;
                          const a =
                            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                            Math.cos((lat1 * Math.PI) / 180) *
                              Math.cos((lat2 * Math.PI) / 180) *
                              Math.sin(dLng / 2) *
                              Math.sin(dLng / 2);
                          const c =
                            2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                          const distance = R * c;
                          if (distance < 20) {
                            const angle =
                              (markerIdx * 2 * Math.PI) /
                              Math.max(1, locations.length);
                            const offsetMeters = 25;
                            const dLatOffset =
                              (offsetMeters / R) *
                              (180 / Math.PI) *
                              Math.cos(angle);
                            const dLngOffset =
                              (offsetMeters /
                                (R * Math.cos((lat1 * Math.PI) / 180))) *
                              (180 / Math.PI) *
                              Math.sin(angle);
                            offsetPosition = [
                              lat2 + dLatOffset,
                              lng2 + dLngOffset,
                            ] as LatLngTuple;
                            showDashedLine = true;
                          }
                        }
                        markerIdx++;
                        return [
                          <Marker
                            key={markerIdx}
                            position={offsetPosition as LatLngTuple}
                          >
                            <CustomPopup
                              imageUrl={location.imageUrl}
                              date={location.date}
                              author={location.author}
                              altText={`Photo by ${location.author}`}
                              contentText={location.contentText}
                            />
                            {showDashedLine && userPosition && (
                              <Polyline
                                positions={[
                                  userPosition as LatLngTuple,
                                  location.position as LatLngTuple,
                                ]}
                                pathOptions={{
                                  color: "gray",
                                  dashArray: "6 6",
                                  weight: 2,
                                  opacity: 0.7,
                                }}
                              />
                            )}
                          </Marker>,
                        ];
                      } else {
                        return group.map((location, i) => {
                          const angle = (i * 2 * Math.PI) / group.length;
                          const offsetMeters = 18;
                          const R = 6371000;
                          const dLatOffset =
                            (offsetMeters / R) *
                            (180 / Math.PI) *
                            Math.cos(angle);
                          const dLngOffset =
                            (offsetMeters /
                              (R *
                                Math.cos(
                                  (location.position[0] * Math.PI) / 180
                                ))) *
                            (180 / Math.PI) *
                            Math.sin(angle);
                          const offsetPosition = [
                            location.position[0] + dLatOffset,
                            location.position[1] + dLngOffset,
                          ] as LatLngTuple;
                          markerIdx++;
                          return (
                            <Marker
                              key={markerIdx}
                              position={offsetPosition as LatLngTuple}
                            >
                              <CustomPopup
                                imageUrl={location.imageUrl}
                                date={location.date}
                                author={location.author}
                                altText={`Photo by ${location.author}`}
                                contentText={location.contentText}
                              />
                              <Polyline
                                positions={[
                                  offsetPosition as LatLngTuple,
                                  location.position as LatLngTuple,
                                ]}
                                pathOptions={{
                                  color: "gray",
                                  dashArray: "4 6",
                                  weight: 1,
                                  opacity: 0.5,
                                }}
                              />
                            </Marker>
                          );
                        });
                      }
                    });
                  })()}

                {userPosition && (
                  <>
                    <Marker
                      position={userPosition}
                      icon={userIcon}
                      zIndexOffset={1000}
                    >
                      <Popup>Your current location</Popup>
                    </Marker>
                    <CenterMap position={userPosition} />
                  </>
                )}
              </MapContainer>

              {error && (
                <div className="absolute inset-0 bg-red-500/50 flex items-center justify-center z-50 rounded-3xl">
                  <div className="text-white text-xl text-center">
                    Error loading locations: {error}
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
      <Footer userPosition={userPosition} />
    </div>
  );
}
