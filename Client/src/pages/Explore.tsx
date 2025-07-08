import { useState, useEffect } from 'react';
import { Search, Dot, XCircle, Loader } from 'lucide-react';
import axios from 'axios';

export default function Explore() {
  const [tempSearch, setTempSearch] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    habitat: '',
    type: ''
  });
  const [speciesData, setSpeciesData] = useState([]);
  const [habitats, setHabitats] = useState<{ id: number; name: string }[]>([]);
  const [specieTypes, setSpecieTypes] = useState<{ id: number; name: string }[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const timerId = setTimeout(() => {
      setSearchQuery(tempSearch);
    }, 500);
    return () => clearTimeout(timerId);
  }, [tempSearch]);

  useEffect(() => {
    const fetchAllSpecies = async () => {
      try {
        const response = await axios.get(
          `${import.meta.env.VITE_SERVER_ENDPOINT}/api/species`,
          {
            headers: {
              "ngrok-skip-browser-warning": "please-ngrok-we-love-you",
              Authorization: `Bearer ${localStorage.getItem("authToken")}`
            }
          }
        );
        const data = response.data.data;
        const uniqueHabitats: { id: number, name: string }[] = [];
        const uniqueTypes: { id: number, name: string }[] = [];
        data.forEach((loc: any) => {
          if (loc.habitat && !uniqueHabitats.some(h => h.id === loc.habitat.id)) {
            uniqueHabitats.push({ id: loc.habitat.id, name: loc.habitat.name });
          }
          if (Array.isArray(loc.specie_types)) {
            loc.specie_types.forEach((t: any) => {
              if (!uniqueTypes.some(type => type.id === t.id)) {
                uniqueTypes.push({ id: t.id, name: t.name });
              }
            });
          }
        });
        setHabitats(uniqueHabitats);
        setSpecieTypes(uniqueTypes);
      } catch (err) {
        setError(
          axios.isAxiosError(err)
            ? err.response?.data?.message || err.message
            : 'Failed to load species data'
        );
      }
    };
    fetchAllSpecies();
  }, []);

  useEffect(() => {
    const fetchSpecies = async () => {
      setLoading(true);
      setError(null);
      try {
        const params: Record<string, string> = {};
        if (searchQuery) params.search = searchQuery;
        if (filters.habitat) params.habitat_id = filters.habitat;
        if (filters.type) params.specie_type_id = filters.type;

        const response = await axios.get(
          `${import.meta.env.VITE_SERVER_ENDPOINT}/api/species`,
          {
            params,
            headers: {
              "ngrok-skip-browser-warning": "please-ngrok-we-love-you",
              Authorization: `Bearer ${localStorage.getItem("authToken")}`
            }
          }
        );

        const data = response.data.data;
        let filteredData = data;
        if (filters.type) {
          filteredData = data.filter((loc: any) =>
            Array.isArray(loc.specie_types) &&
            loc.specie_types.some((t: any) => String(t.id) === String(filters.type))
          );
        }
        if (filters.habitat) {
          filteredData = filteredData.filter((loc: any) =>
            loc.habitat && String(loc.habitat.id) === String(filters.habitat)
          );
        }
        const formattedData = filteredData.map((loc: any) => {
          const specieName = loc.common_name || "Unknown species";
          const firstImage = loc.image?.url || "";
          return {
            id: loc.id,
            imageUrl: firstImage,
            date: loc.created_at ? new Date(loc.created_at) : null,
            author: loc.user?.name || "Unknown",
            contentText: loc?.scientific_name,
            altText: specieName,
            habitat: loc?.habitat?.name || "Unknown",
            habitatId: loc?.habitat?.id || null,
            specieKingdom: loc?.specie_kingdom?.name || "Unknown",
            specieTypes: Array.isArray(loc.specie_types) ? loc.specie_types.map((t: any) => t.name) : [],
            specieTypeIds: Array.isArray(loc.specie_types) ? loc.specie_types.map((t: any) => t.id) : [],
          };
        });
        setSpeciesData(formattedData);
        setError(null);
      } catch (err) {
        setError(
          axios.isAxiosError(err)
            ? err.response?.data?.message || err.message
            : 'Failed to load species'
        );
      } finally {
        setLoading(false);
      }
    };
    fetchSpecies();
  }, [searchQuery, filters.habitat, filters.type]);

  const clearFilters = () => {
    setFilters({ habitat: '', type: '' });
    setTempSearch('');
  };

  return (
    <div className="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
      <div className="max-w-6xl mx-auto">
        <div className="mb-8 space-y-4">
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
            Explore Species
          </h1>
          
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search className="h-5 w-5 text-gray-400" />
            </div>
            <input
              type="text"
              placeholder="Search species..."
              className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              value={tempSearch}
              onChange={(e) => setTempSearch(e.target.value)}
            />
            {tempSearch && (
              <button
                onClick={clearFilters}
                className="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <XCircle className="h-5 w-5 text-gray-400 hover:text-gray-500" />
              </button>
            )}
          </div>

          <div className="flex flex-wrap gap-3">
            <select
              value={filters.habitat}
              onChange={(e) => setFilters({ ...filters, habitat: e.target.value })}
              className="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">All Habitats</option>
              {habitats.map((habitat: any) => (
                <option key={habitat.id} value={habitat.id}>
                  {habitat.name}
                </option>
              ))}
            </select>

            <select
              value={filters.type}
              onChange={(e) => setFilters({ ...filters, type: e.target.value })}
              className="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500"
            >
              <option value="">All Types</option>
              {specieTypes.map((type: any) => (
                <option key={type.id} value={type.id}>
                  {type.name}
                </option>
              ))}
            </select>

            <button
              onClick={clearFilters}
              className="px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:border-emerald-400"
            >
              <XCircle className="h-4 w-4 inline-block mr-1" />
              Clear Filters
            </button>
          </div>
        </div>

        {loading && (
          <div className="flex justify-center items-center h-32">
            <Loader className="h-8 w-8 text-emerald-500 animate-spin" />
          </div>
        )}

        {error && (
          <div className="p-4 bg-red-50 text-red-600 rounded-lg">
            Error: {error}
          </div>
        )}

        {!loading && !error && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {speciesData.map((species: any) => (
              <div
                key={species.id}
                className="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200"
              >
                <div className="relative aspect-video overflow-hidden rounded-t-xl">
                  <img
                    src={species.imageUrl}
                    alt={species.altText}
                    className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                  />
                  <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                    <h3 className="text-lg font-semibold text-white">
                      {species.altText}
                    </h3>
                  </div>
                </div>
                <div className="p-4">
                  <div className="flex items-center justify-between text-sm text-gray-600">
                    <span className="inline-flex items-center">
                      <Dot className="h-4 w-4 mr-1" />
                      {species.contentText}
                    </span>
                  </div>
                  {species.specieTypes && species.specieTypes.length > 0 && (
                    <div className="mt-1 text-xs text-emerald-600 font-medium">
                      {species.specieTypes.join(", ")}
                    </div>
                  )}
                  <p className="mt-2 text-sm text-gray-500">
                    Found in {species.habitat}
                  </p>
                </div>
              </div>
            ))}
          </div>
        )}

        {!loading && !error && speciesData.length === 0 && (
          <div className="text-center py-12 text-gray-500">
            No species found matching your criteria
          </div>
        )}
      </div>
    </div>
  );
}
