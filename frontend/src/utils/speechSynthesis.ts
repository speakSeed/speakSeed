/**
 * Web Speech API utilities for text-to-speech
 */

export interface SpeechOptions {
  rate?: number; // 0.1 to 10
  pitch?: number; // 0 to 2
  volume?: number; // 0 to 1
  lang?: string;
  onStart?: (event: SpeechSynthesisEvent) => void;
  onEnd?: (event: SpeechSynthesisEvent) => void;
  onError?: (event: SpeechSynthesisErrorEvent) => void;
}

export const speech = {
  /**
   * Speak a word using Web Speech API
   */
  speak(text: string, options: SpeechOptions = {}): void {
    if (!("speechSynthesis" in window)) {
      console.warn("Speech synthesis not supported");
      return;
    }

    // Cancel any ongoing speech
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);

    // Options
    utterance.rate = options.rate || 1.0;
    utterance.pitch = options.pitch || 1.0;
    utterance.volume = options.volume || 1.0;
    utterance.lang = options.lang || "en-US";

    // Event handlers
    if (options.onStart) utterance.onstart = options.onStart;
    if (options.onEnd) utterance.onend = options.onEnd;
    if (options.onError) utterance.onerror = options.onError;

    window.speechSynthesis.speak(utterance);
  },

  /**
   * Spell out a word letter by letter
   */
  spellOut(text: string, delay = 400): void {
    if (!("speechSynthesis" in window)) {
      console.warn("Speech synthesis not supported");
      return;
    }

    window.speechSynthesis.cancel();

    const letters = text.split("");
    let currentIndex = 0;

    const speakNextLetter = () => {
      if (currentIndex < letters.length) {
        const utterance = new SpeechSynthesisUtterance(letters[currentIndex]);
        utterance.rate = 0.8;
        utterance.lang = "en-US";

        utterance.onend = () => {
          currentIndex++;
          setTimeout(speakNextLetter, delay);
        };

        window.speechSynthesis.speak(utterance);
      }
    };

    speakNextLetter();
  },

  /**
   * Stop any ongoing speech
   */
  stop(): void {
    if ("speechSynthesis" in window) {
      window.speechSynthesis.cancel();
    }
  },

  /**
   * Check if speech synthesis is available
   */
  isSupported(): boolean {
    return "speechSynthesis" in window;
  },

  /**
   * Get available voices
   */
  getVoices(): Promise<SpeechSynthesisVoice[]> {
    return new Promise((resolve) => {
      let voices = window.speechSynthesis.getVoices();

      if (voices.length) {
        resolve(voices);
      } else {
        window.speechSynthesis.onvoiceschanged = () => {
          voices = window.speechSynthesis.getVoices();
          resolve(voices);
        };
      }
    });
  },
};

export default speech;
