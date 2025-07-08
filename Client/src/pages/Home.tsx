import {
  Search,
  PawPrint,
  Leaf,
  Trees,
  BrainCircuit,
  Sparkles,
  ArrowRight,
  Camera,
  Globe,
} from "lucide-react";
import { Link } from "react-router";

export default function Home() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-emerald-50 to-blue-50 font-['Atkinson Hyperlegible Mono',sans-serif]">
      <section className="pt-28 pb-20 px-4 md:px-8">
        <div className="max-w-6xl mx-auto">
          <div className="flex flex-col md:flex-row items-center gap-12">
            <div className="flex-1 space-y-6">
              <h1 className="text-4xl md:text-6xl font-bold text-gray-900 leading-tight tracking-tight">
                <span className="title-animate">Identify any</span>{" "}
                <span className="text-emerald-600 relative mx-2 title-animate title-animate-delay-1">
                  <span className="relative z-10">plant or animal</span>
                  <span className="absolute bottom-2 left-0 w-full h-3 bg-emerald-100 -z-0 opacity-70"></span>
                </span>{" "}
                <span className="title-animate title-animate-delay-2">in seconds</span>
              </h1>
              <p className="text-lg text-gray-600 max-w-xl leading-relaxed">
                Our advanced AI instantly identifies 10,000+ plant and animal species from a single photo. Join thousands of researchers, educators, and nature enthusiasts.
              </p>
              
              <div className="flex flex-col sm:flex-row gap-4 pt-6">
                <Link
                  to="/register"
                  className="border border-emerald-600 font-medium py-3 px-8 rounded-lg bg-emerald-600 text-white shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all hover:-translate-y-1 text-center flex items-center justify-center space-x-2"
                >
                  <span>Start for Free</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
              
              <div className="pt-6 flex items-center text-sm text-gray-500">
                <div className="flex -space-x-2 mr-3">
                  <img src="https://randomuser.me/api/portraits/women/17.jpg" className="w-8 h-8 rounded-full border-2 border-white" />
                  <img src="https://randomuser.me/api/portraits/men/32.jpg" className="w-8 h-8 rounded-full border-2 border-white" />
                  <img src="https://randomuser.me/api/portraits/women/28.jpg" className="w-8 h-8 rounded-full border-2 border-white" />
                </div>
                <span>Join 100+ researchers already using our platform</span>
              </div>
            </div>
            
            <div className="flex-1 relative">
              <div className="absolute -top-10 -left-10 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
              <div className="absolute -bottom-10 -right-10 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
              <div className="absolute inset-0 w-64 h-64 mx-auto my-auto bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
              <div className="relative backdrop-blur-sm bg-white/30 border border-white/20 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300">
                <div className="grid grid-cols-2 gap-8">
                  <div className="flex flex-col items-center p-4 bg-white/60 rounded-xl shadow-sm hover:bg-white/80 hover:shadow-md transition-all">
                    <Leaf className="w-12 h-12 text-green-600 mb-3" />
                    <h3 className="font-medium text-center text-gray-900">
                      Plant Classification
                    </h3>
                    <p className="text-xs text-center text-gray-600 mt-1">AI-powered recognition</p>
                  </div>
                  <div className="flex flex-col items-center p-4 bg-white/60 rounded-xl shadow-sm hover:bg-white/80 hover:shadow-md transition-all">
                    <Search className="w-12 h-12 text-blue-600 mb-3" />
                    <h3 className="font-medium text-center text-gray-900">Data Analysis</h3>
                    <p className="text-xs text-center text-gray-600 mt-1">Detailed insights</p>
                  </div>
                  <div className="flex flex-col items-center p-4 bg-white/60 rounded-xl shadow-sm hover:bg-white/80 hover:shadow-md transition-all">
                    <PawPrint className="w-12 h-12 text-pink-600 mb-3" />
                    <h3 className="font-medium text-center text-gray-900">
                      Animal Detection
                    </h3>
                    <p className="text-xs text-center text-gray-600 mt-1">95% accuracy</p>
                  </div>
                  <div className="flex flex-col items-center p-4 bg-white/60 rounded-xl shadow-sm hover:bg-white/80 hover:shadow-md transition-all">
                    <Trees className="w-12 h-12 text-teal-600 mb-3" />
                    <h3 className="font-medium text-center text-gray-900">
                      GPS Mapping
                    </h3>
                    <p className="text-xs text-center text-gray-600 mt-1">Location tracking</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-20 px-4 md:px-8 bg-white/50">
        <div className="max-w-6xl mx-auto">
          <div className="text-center mb-16">
            <div className="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 font-medium text-sm tracking-wide shadow-sm mb-6">
              <Sparkles className="w-4 h-4 mr-2" />
              Powered by Advanced AI
            </div>
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
              Discover the Power of 
              <span className="relative text-emerald-600 mx-2">
                <span className="relative z-10">AI Recognition</span>
                <span className="absolute bottom-1 left-0 w-full h-3 bg-emerald-100 -z-0 opacity-70"></span>
              </span>
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
              Our platform combines advanced computer vision and machine learning to identify species with remarkable accuracy.
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            <div className="backdrop-blur-sm bg-white/40 border border-white/20 rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              <div className="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6 shadow-inner">
                <Camera className="w-9 h-9 text-purple-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-3">
                Instant Recognition
              </h3>
              <p className="text-gray-600 leading-relaxed">
                Just snap a photo of any plant or animal, and our AI will instantly identify the species with remarkable accuracy.
              </p>
            </div>

            <div className="backdrop-blur-sm bg-white/40 border border-white/20 rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              <div className="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6 shadow-inner">
                <Globe className="w-9 h-9 text-blue-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-3">
                Global Database
              </h3>
              <p className="text-gray-600 leading-relaxed">
                Access our extensive database of over 10,000 plant and animal species from around the world.
              </p>
            </div>

            <div className="backdrop-blur-sm bg-white/40 border border-white/20 rounded-xl p-8 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              <div className="w-16 h-16 bg-pink-100 rounded-lg flex items-center justify-center mb-6 shadow-inner">
                <BrainCircuit className="w-9 h-9 text-pink-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-3">
                AI Identification
              </h3>
              <p className="text-gray-600 leading-relaxed">
                Our advanced neural networks analyze thousands of features to accurately classify species in milliseconds.
              </p>
            </div>
          </div>
          
          <div className="text-center mt-16">
            <Link
              to="/register"
              className="inline-flex items-center space-x-2 border border-emerald-600 font-medium py-3 px-8 rounded-lg bg-emerald-600 text-white shadow-lg shadow-emerald-200 hover:shadow-emerald-300 transition-all hover:-translate-y-1 text-center"
            >
              <span>Join Our Community Today</span>
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
