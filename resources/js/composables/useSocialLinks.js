export const useSocialLinks = () => {
  const socialNetworks = {
    tiktok: {
      url: 'https://www.tiktok.com/@isotopenergy.md',
      icon: ['fab', 'tiktok'],
      name: 'TikTok',
      show: true,
    },
    instagram: {
      url: 'https://www.instagram.com/isotopenergymd',
      icon: ['fab', 'instagram'],
      name: 'Instagram',
      show: false
    },
    facebook: {
      url: 'https://www.facebook.com/isotopenergymd',
      icon: ['fab', 'facebook'],
      name: 'Facebook',
      show: false,
    },
    whatsapp: {
      url: 'https://wa.me/37360123456',
      icon: ['fab', 'whatsapp'],
      name: 'WhatsApp',
      show: false,
    },
    telegram: {
      url: 'https://t.me/isotopenergymd',
      icon: ['fab', 'telegram'],
      name: 'Telegram',
      show: false,
    },
  }

  // Получить только активные соцсети
  const getActiveSocials = () => {
    return Object.entries(socialNetworks)
      .filter(([key, social]) => social.show)
      .map(([key, social]) => ({ key, ...social }))
  }

  // Получить все соцсети (для админки в будущем)
  const getAllSocials = () => {
    return Object.entries(socialNetworks).map(([key, social]) => ({ key, ...social }))
  }

  return {
    socialNetworks,
    getActiveSocials,
    getAllSocials,
  }
}
